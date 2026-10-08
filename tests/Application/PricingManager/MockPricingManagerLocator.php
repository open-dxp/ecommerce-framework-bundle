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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\Tests\Application\PricingManager;

use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\PricingManagerInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\PricingManagerLocatorInterface;

/**
 * Hands out the same pricing manager for every tenant.
 */
final readonly class MockPricingManagerLocator implements PricingManagerLocatorInterface
{
    public function __construct(private PricingManagerInterface $pricing)
    {
    }

    public function getPricingManager(?string $tenant = null): PricingManagerInterface
    {
        return $this->pricing;
    }

    public function hasPricingManager(string $tenant): bool
    {
        return true;
    }
}
