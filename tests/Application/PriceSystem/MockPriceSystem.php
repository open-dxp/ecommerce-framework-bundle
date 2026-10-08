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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\Tests\Application\PriceSystem;

use OpenDxp\Bundle\EcommerceFrameworkBundle\Environment;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\CheckoutableInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\Currency;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem\AttributePriceSystem;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem\Price;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem\PriceInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\PricingManagerLocatorInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Type\Decimal;
use OpenDxp\Localization\LocaleService;
use OpenDxp\Model\DataObject\OnlineShopTaxClass;

/**
 * Answers every product with the given gross price in euros and applies the rules of the pricing manager.
 */
final class MockPriceSystem extends AttributePriceSystem
{
    public function __construct(
        PricingManagerLocatorInterface $pricingManagers,
        private readonly Decimal $grossPrice,
        private readonly OnlineShopTaxClass $taxClass,
    ) {
        parent::__construct($pricingManagers, new Environment(new LocaleService()));
    }

    public function getTaxClassForProduct(CheckoutableInterface $product): OnlineShopTaxClass
    {
        return $this->taxClass;
    }

    protected function calculateAmount(CheckoutableInterface $product, array $products): Decimal
    {
        return $this->grossPrice;
    }

    protected function getPriceClassInstance(Decimal $amount): PriceInterface
    {
        return new Price($amount, new Currency('EUR'));
    }
}
