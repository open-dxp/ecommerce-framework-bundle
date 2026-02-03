<?php

declare(strict_types=1);

/**
 * OpenDXP
 *
 * This source file is licensed under the GNU General Public License version 3 (GPLv3).
 *
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) Pimcore GmbH (https://pimcore.com)
 * @copyright  Modification Copyright (c) OpenDXP (https://www.opendxp.io)
 * @license    https://www.gnu.org/licenses/gpl-3.0.html  GNU General Public License version 3 (GPLv3)
 */

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\DependencyInjection\ServiceLocator;

use OpenDxp\Bundle\EcommerceFrameworkBundle\EnvironmentInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Exception\UnsupportedException;
use Psr\Container\ContainerInterface as PsrContainerInterface;

abstract class TenantAwareServiceLocator
{
    protected string $defaultTenant = 'default';

    public function __construct(
        protected PsrContainerInterface $locator,
        protected EnvironmentInterface $environment,
        /**
         * If true the locator will not fall back to the default tenant if a tenant is requested but not existing
         */
        protected bool $strictTenants = false
    ) {
    }

    protected function locate(?string $tenant = null): mixed
    {
        $tenant = $this->resolveTenant($tenant);

        if (!$this->locator->has($tenant)) {
            throw $this->buildNotFoundException($tenant);
        }

        return $this->locator->get($tenant);
    }

    abstract protected function buildNotFoundException(string $tenant): UnsupportedException;

    protected function resolveTenant(?string $tenant = null): string
    {
        // explicitly checking for empty here to catch situations where the tenant is just an empty string
        if (empty($tenant)) {
            $tenant = $this->getEnvironmentTenant();
        }

        // if tenant isn't available and we're not in strict tenant mode, fall
        // back to the default tenant
        // in strict tenant mode, just return the tenant, no matter if it exists or not
        if (!empty($tenant) && ($this->strictTenants || $this->locator->has($tenant))) {
            return $tenant;
        }

        return $this->defaultTenant;
    }

    abstract protected function getEnvironmentTenant(): ?string;
}
