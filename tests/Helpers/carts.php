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

use OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartPriceCalculator;
use OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartPriceModificator\Shipping;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Environment;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\CheckoutableInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\PricingManagerInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Tests\Application\CartManager\MockSessionCart;
use OpenDxp\Localization\LocaleService;
use OpenDxp\Model\DataObject\OnlineShopTaxClass;

/**
 * @return MockSessionCart a cart holding one of each product
 */
function cart(CheckoutableInterface ...$products): MockSessionCart
{
    $cart = new MockSessionCart();
    $cart->setPriceCalculator(new CartPriceCalculator(new Environment(new LocaleService()), $cart));

    foreach ($products as $product) {
        $cart->addItem($product, 1);
    }

    return $cart;
}

function pricedBy(MockSessionCart $cart, PricingManagerInterface $pricing): MockSessionCart
{
    $cart->getPriceCalculator()->setPricingManager($pricing);

    return $cart;
}

/**
 * @return MockSessionCart the cart with a shipping charge of 10 euros
 */
function withShipping(MockSessionCart $cart): MockSessionCart
{
    $cart->getPriceCalculator()->addModificator(new Shipping(['charge' => 10]));

    return $cart;
}

/**
 * @return MockSessionCart the cart with a shipping charge of 10 euros, taxed by the tax class
 */
function withTaxedShipping(MockSessionCart $cart, OnlineShopTaxClass $taxClass): MockSessionCart
{
    $shipping = new Shipping(['charge' => 10]);
    $shipping->setTaxClass($taxClass);
    $cart->getPriceCalculator()->addModificator($shipping);

    return $cart;
}
