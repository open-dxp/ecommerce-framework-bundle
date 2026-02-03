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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\FilterService;

use OpenDxp\Bundle\EcommerceFrameworkBundle\DependencyInjection\ServiceLocator\AssortmentTenantAwareServiceLocator;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Exception\UnsupportedException;

class FilterServiceLocator extends AssortmentTenantAwareServiceLocator implements FilterServiceLocatorInterface
{
    public function getFilterService(string $tenant = null): FilterService
    {
        return $this->locate($tenant);
    }

    public function hasFilterService(string $tenant): bool
    {
        return $this->locator->has($tenant);
    }

    protected function buildNotFoundException(string $tenant): UnsupportedException
    {
        return new UnsupportedException(sprintf(
            'Filter service for assortment tenant "%s" is not registered. Please check the configuration.',
            $tenant
        ));
    }
}
