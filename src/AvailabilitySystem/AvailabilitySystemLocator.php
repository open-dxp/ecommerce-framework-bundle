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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\AvailabilitySystem;

use OpenDxp\Bundle\EcommerceFrameworkBundle\DependencyInjection\ServiceLocator\NameServiceLocator;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Exception\UnsupportedException;

class AvailabilitySystemLocator extends NameServiceLocator implements AvailabilitySystemLocatorInterface
{
    public function getAvailabilitySystem(string $name = null): AvailabilitySystemInterface
    {
        return $this->locate($name);
    }

    public function hasAvailabilitySystem(string $name): bool
    {
        return $this->locator->has($name);
    }

    protected function buildNotFoundException(string $name): UnsupportedException
    {
        return new UnsupportedException(sprintf(
            'Availability system "%s" is not supported. Please check the configuration.',
            $name
        ));
    }
}
