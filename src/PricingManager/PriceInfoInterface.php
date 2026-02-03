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

use OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem\PriceInfoInterface as PriceSystemPriceInfoInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem\PriceInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Type\Decimal;

interface PriceInfoInterface extends PriceSystemPriceInfoInterface
{
    public function __construct(PriceSystemPriceInfoInterface $priceInfo, EnvironmentInterface $environment);

    /**
     * @return $this
     */
    public function addRule(RuleInterface $rule): static;

    /**
     * Returns all valid rules, if forceRecalc, recalculation of valid rules is forced
     *
     *
     * @return RuleInterface[]
     */
    public function getRules(bool $forceRecalc = false): array;

    /**
     * @return $this
     */
    public function setAmount(Decimal $amount): static;

    public function getAmount(): Decimal;

    public function getOriginalPrice(): PriceInterface;

    public function getOriginalTotalPrice(): PriceInterface;

    public function getEnvironment(): EnvironmentInterface;

    /**
     * @return $this
     */
    public function setEnvironment(EnvironmentInterface $environment): static;

    public function hasDiscount(): bool;

    public function getDiscount(): PriceInterface;

    public function getTotalDiscount(): PriceInterface;

    /**
     * Get discount in percent
     */
    public function getDiscountPercent(): float;

    public function hasRulesApplied(): bool;
}
