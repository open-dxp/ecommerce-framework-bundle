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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\Tests\Feature\PricingManager;

use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\Action\FreeShipping;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Tests\Factory\ProductFactory;

describe('a product discount of 10', function () {
    beforeEach(function () {
        $this->pricing = pricingManager(rule(productDiscount(10)));
    });

    it('takes the discount off the price of a product', function () {
        $product = productsCosting($this->pricing, 100)[0];

        $info = $product->getOSPriceInfo(2);

        expect($info)
            ->getPrice()
            ->getAmount()
            ->toBeAmount(90)
            ->getTotalPrice()
            ->getAmount()
            ->toBeAmount(180);
    });

    it('takes the discount off the products in a cart', function () {
        $cart = pricedCart($this->pricing, 100);

        expect($cart)->toCost(subTotal: 90, grandTotal: 90);
    });

    it('charges the shipping on top', function () {
        $cart = withShipping(pricedCart($this->pricing, 100));

        expect($cart)->toCost(subTotal: 90, grandTotal: 100);
    });
});

describe('a cart discount of 10', function () {
    beforeEach(function () {
        $this->pricing = pricingManager(rule(cartDiscount(10)));
    });

    it('leaves the price of a product alone', function () {
        $product = productsCosting($this->pricing, 100)[0];

        $info = $product->getOSPriceInfo(2);

        expect($info)
            ->getPrice()
            ->getAmount()
            ->toBeAmount(100)
            ->getTotalPrice()
            ->getAmount()
            ->toBeAmount(200);
    });

    it('takes the discount off the total of a cart', function () {
        $cart = pricedCart($this->pricing, 100, 40);

        expect($cart)->toCost(subTotal: 140, grandTotal: 130);
    });

    it('takes the discount off the shipping as well', function () {
        $cart = withShipping(pricedCart($this->pricing, 100, 40));

        expect($cart)->toCost(subTotal: 140, grandTotal: 140);
    });
});

describe('a cart discount of 10 for carts from 200 euros', function () {
    beforeEach(function () {
        $this->pricing = pricingManager(ruleWhen(cartAmountOfAtLeast(200), cartDiscount(10)));
    });

    it('takes nothing off a smaller cart', function () {
        $cart = withShipping(pricedCart($this->pricing, 100, 40));

        expect($cart)->toCost(subTotal: 140, grandTotal: 150);
    });

    it('takes the discount off a cart that reaches the amount', function () {
        $cart = pricedCart($this->pricing, 200, 40);

        expect($cart)->toCost(subTotal: 240, grandTotal: 230);
    });

    it('takes the discount off the shipping of a cart that reaches the amount', function () {
        $cart = withShipping(pricedCart($this->pricing, 200, 40));

        expect($cart)->toCost(subTotal: 240, grandTotal: 240);
    });
});

it('checks a condition inside a bracket', function () {
    $pricing = pricingManager(ruleWhen(allOf(cartAmountOfAtLeast(200)), cartDiscount(10)));

    $cart = pricedCart($pricing, 200, 40);

    expect($cart)->toCost(subTotal: 240, grandTotal: 230);
});

describe('a product and a cart discount of the same rule', function () {
    beforeEach(function () {
        $this->pricing = pricingManager(rule(cartDiscount(10), productDiscount(15)));
    });

    it('takes the product discount off the price of a product', function () {
        $product = productsCosting($this->pricing, 100)[0];

        $info = $product->getOSPriceInfo(2);

        expect($info)
            ->getPrice()
            ->getAmount()
            ->toBeAmount(85)
            ->getTotalPrice()
            ->getAmount()
            ->toBeAmount(170);
    });

    it('takes both discounts off a cart', function () {
        $cart = pricedCart($this->pricing, 100, 40);

        expect($cart)->toCost(subTotal: 110, grandTotal: 100);
    });

    it('takes the cart discount off the shipping', function () {
        $cart = withShipping(pricedCart($this->pricing, 100, 40));

        expect($cart)->toCost(subTotal: 110, grandTotal: 110);
    });
});

describe('a gift for carts from 200 euros', function () {
    beforeEach(function () {
        $product = ProductFactory::new()
            ->costing(100)
            ->create();
        $this->pricing = pricingManager(ruleWhen(cartAmountOfAtLeast(200), gift($product)));
    });

    it('adds no gift to a smaller cart', function () {
        $cart = withShipping(pricedCart($this->pricing, 100, 40));

        expect($cart)
            ->toCost(subTotal: 140, grandTotal: 150)
            ->getGiftItems()
            ->toBeEmpty();
    });

    it('adds the gift free of charge to a cart that reaches the amount', function () {
        $cart = withShipping(pricedCart($this->pricing, 100, 40, 80));

        expect($cart)
            ->toCost(subTotal: 220, grandTotal: 230)
            ->getGiftItems()
            ->toHaveCount(1);
    });
});

it('adds every gift of a rule', function () {
    $gifts = array_map(gift(...), productsCosting(pricingManager(), 100, 200));
    $pricing = pricingManager(ruleWhen(cartAmountOfAtLeast(200), ...$gifts));

    $cart = withShipping(pricedCart($pricing, 100, 40, 80));

    expect($cart)
        ->toCost(subTotal: 220, grandTotal: 230)
        ->getGiftItems()
        ->toHaveCount(2);
});

describe('free shipping for carts from 200 euros', function () {
    beforeEach(function () {
        $this->pricing = pricingManager(ruleWhen(cartAmountOfAtLeast(200), new FreeShipping()));
    });

    it('charges shipping on a smaller cart', function () {
        $cart = withShipping(pricedCart($this->pricing, 100, 40));

        expect($cart)
            ->toCost(subTotal: 140, grandTotal: 150)
            ->and($cart->getPriceCalculator()->getPriceModifications()['shipping']->getAmount())
            ->toBeAmount(10);
    });

    it('charges no shipping on a cart that reaches the amount', function () {
        $cart = withShipping(pricedCart($this->pricing, 100, 40, 80));

        expect($cart)
            ->toCost(subTotal: 220, grandTotal: 220)
            ->and($cart->getPriceCalculator()->getPriceModifications()['shipping']->getAmount())
            ->toBeAmount(0);
    });
});
