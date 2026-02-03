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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\CheckoutManager;

use OpenDxp\Bundle\EcommerceFrameworkBundle\DependencyInjection\ServiceLocator\CheckoutTenantAwareServiceLocator;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Exception\UnsupportedException;

class CheckoutManagerFactoryLocator extends CheckoutTenantAwareServiceLocator implements CheckoutManagerFactoryLocatorInterface
{
    public function getCheckoutManagerFactory(string $tenant = null): CheckoutManagerFactoryInterface
    {
        return $this->locate($tenant);
    }

    public function hasCheckoutManagerFactory(string $tenant): bool
    {
        return $this->locator->has($tenant);
    }

    protected function buildNotFoundException(string $tenant): UnsupportedException
    {
        return new UnsupportedException(sprintf(
            'There is no factory defined for checkout manager tenant "%s". Please check the configuration.',
            $tenant
        ));
    }
}
