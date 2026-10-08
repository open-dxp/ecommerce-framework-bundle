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

describe('a product discount and a cart discount of two rules', function () {
    beforeEach(function () {
        $this->pricing = pricingManager(rule(productDiscount(10)), rule(cartDiscount(10)));
    });

    it('takes the product discount off the price of a product', function () {
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

    it('takes both discounts off a cart', function () {
        $cart = pricedCart($this->pricing, 100, 50);

        expect($cart)->toCost(subTotal: 130, grandTotal: 120);
    });

    it('takes the cart discount off the shipping', function () {
        $cart = withShipping(pricedCart($this->pricing, 100, 50));

        expect($cart)->toCost(subTotal: 130, grandTotal: 130);
    });
});

describe('a product discount and a cart discount for carts from 200 euros', function () {
    beforeEach(function () {
        $this->pricing = pricingManager(
            rule(productDiscount(10)),
            ruleWhen(cartAmountOfAtLeast(200), cartDiscount(10)),
        );
    });

    it('takes only the product discount off a smaller cart', function () {
        $cart = withShipping(pricedCart($this->pricing, 100, 50));

        expect($cart)->toCost(subTotal: 130, grandTotal: 140);
    });

    it('takes both discounts off a cart that reaches the amount', function () {
        $cart = pricedCart($this->pricing, 100, 50, 80);

        expect($cart)->toCost(subTotal: 200, grandTotal: 190);
    });

    it('takes the cart discount off the shipping of a cart that reaches the amount', function () {
        $cart = withShipping(pricedCart($this->pricing, 100, 50, 80));

        expect($cart)->toCost(subTotal: 200, grandTotal: 200);
    });
});

dataset('rules with free shipping first or last', [
    'free shipping first' => [
        fn () => [
            rule(productDiscount(10)),
            ruleWhen(cartAmountOfAtLeast(200), new FreeShipping()),
            ruleWhen(cartAmountOfAtLeast(10), productDiscount(10)),
        ],
    ],
    'free shipping last' => [
        fn () => [
            rule(productDiscount(10)),
            ruleWhen(cartAmountOfAtLeast(10), productDiscount(10)),
            ruleWhen(cartAmountOfAtLeast(200), new FreeShipping()),
        ],
    ],
]);

it('charges shipping on a smaller cart, whatever the order of the rules', function (array $rules) {
    $pricing = pricingManager(...$rules);

    $cart = withShipping(pricedCart($pricing, 100, 50));

    expect($cart)->toCost(subTotal: 130, grandTotal: 140);
})->with('rules with free shipping first or last');

it('charges no shipping on a cart that reaches the amount, whatever the order of the rules', function (array $rules) {
    $pricing = pricingManager(...$rules);

    $cart = withShipping(pricedCart($pricing, 100, 50, 80));

    expect($cart)->toCost(subTotal: 200, grandTotal: 200);
})->with('rules with free shipping first or last');

describe('a product discount for the products 4 and 5 together', function () {
    beforeEach(function () {
        $this->pricing = pricingManager(ruleWhen(allOf(catalogProduct(5), catalogProduct(4)), productDiscount(10)));
        $this->four = ProductFactory::new()
            ->withId(4)
            ->costing(100)
            ->pricedBy($this->pricing)
            ->create();
        $this->five = ProductFactory::new()
            ->withId(5)
            ->costing(50)
            ->pricedBy($this->pricing)
            ->create();
    });

    it('takes nothing off a single product, which is never both', function () {
        $prices = [
            $this->four->getOSPrice()->getAmount(),
            $this->five->getOSPrice()->getAmount(),
        ];

        expect($prices[0])
            ->toBeAmount(100)
            ->and($prices[1])
            ->toBeAmount(50);
    });

    it('takes nothing off the products in a cart that holds both', function () {
        $cart = withShipping(pricedBy(cart($this->four, $this->five), $this->pricing));

        expect($cart)->toCost(subTotal: 150, grandTotal: 160);
    });
});

describe('a product discount for the product 4 or 5', function () {
    beforeEach(function () {
        $this->pricing = pricingManager(ruleWhen(anyOf(catalogProduct(5), catalogProduct(4)), productDiscount(10)));
        $this->four = ProductFactory::new()
            ->withId(4)
            ->costing(100)
            ->pricedBy($this->pricing)
            ->create();
        $this->five = ProductFactory::new()
            ->withId(5)
            ->costing(50)
            ->pricedBy($this->pricing)
            ->create();
    });

    it('takes the discount off either product', function () {
        $prices = [
            $this->four->getOSPrice()->getAmount(),
            $this->five->getOSPrice()->getAmount(),
        ];

        expect($prices[0])
            ->toBeAmount(90)
            ->and($prices[1])
            ->toBeAmount(40);
    });

    it('takes the discount off both products in a cart', function () {
        $cart = withShipping(pricedBy(cart($this->four, $this->five), $this->pricing));

        expect($cart)->toCost(subTotal: 130, grandTotal: 140);
    });
});
