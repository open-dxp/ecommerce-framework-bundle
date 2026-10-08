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

use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\PricingManager;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\Rule;
use OpenDxp\TestFoundation\Container;

/**
 * Applies the given rules instead of the rules stored in the database.
 */
final class MockPricingManager extends PricingManager
{
    /**
     * @param list<Rule> $givenRules
     */
    public function __construct(private readonly array $givenRules)
    {
        parent::__construct(
            Container::parameter('opendxp_ecommerce.pricing_manager.condition_mapping'),
            Container::parameter('opendxp_ecommerce.pricing_manager.action_mapping'),
        );
    }

    public function getValidRules(): array
    {
        return $this->givenRules;
    }
}
