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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager;

use OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Exception\InvalidConfigException;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem\PriceInfoInterface as PriceSystemPriceInfoInterface;

interface PricingManagerInterface
{
    public function applyProductRules(PriceSystemPriceInfoInterface $priceinfo): PriceSystemPriceInfoInterface;

    /**
     * @return RuleInterface[] applied rules
     */
    public function applyCartRules(CartInterface $cart): array;

    /**
     * Get map from action name to used class
     */
    public function getActionMapping(): array;

    /**
     * Get map from condition name to used class
     */
    public function getConditionMapping(): array;

    /**
     * Factory
     *
     *
     *
     * @throws InvalidConfigException
     */
    public function getCondition(string $type): ConditionInterface;

    /**
     * Factory
     */
    public function getAction(string $type): ActionInterface;

    /**
     * Factory
     */
    public function getEnvironment(): EnvironmentInterface;

    /**
     * Wraps price info in pricing manager price info
     */
    public function getPriceInfo(PriceSystemPriceInfoInterface $priceInfo): PriceInfoInterface|PriceSystemPriceInfoInterface;
}
