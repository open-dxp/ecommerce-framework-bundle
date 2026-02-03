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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem;

use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\Currency;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\RuleInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Type\Decimal;

class ModificatedPrice extends Price implements ModificatedPriceInterface
{
    protected ?RuleInterface $rule = null;

    public function __construct(
        Decimal $amount,
        Currency $currency,
        bool $minPrice = false,
        protected ?string $description = null
    ) {
        parent::__construct($amount, $currency, $minPrice);
    }

    public function getRule(): ?RuleInterface
    {
        return $this->rule;
    }

    /**
     * @return $this
     */
    public function setRule(?RuleInterface $rule): static
    {
        $this->rule = $rule;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description ?? '';
    }

    /**
     * @return $this
     */
    public function setDescription(?string $description = null): static
    {
        $this->description = $description;

        return $this;
    }
}
