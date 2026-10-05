<?php

declare(strict_types=1);

it('applies a product and a cart discount of two rules', function () {
    $pricing = pricingManager(rule(productDiscount(10)), rule(cartDiscount(10)));
    $cart = fn () => pricedBy(cart(product(100, $pricing), product(50, $pricing)), $pricing);

    expect(product(100, $pricing)->getOSPriceInfo(2))
        ->getPrice()->getAmount()->toBeAmount(90)
        ->getTotalPrice()->getAmount()->toBeAmount(180)
        ->and($cart())->toCost(subTotal: 130, grandTotal: 120)
        ->and(withShipping($cart()))->toCost(subTotal: 130, grandTotal: 130);
});

describe('a product discount and a cart discount for carts from 200 euros', function () {
    beforeEach(function () {
        $this->pricing = pricingManager(rule(productDiscount(10)), rule(cartDiscount(10), cartAmountOfAtLeast(200)));
    });

    it('applies only the product discount to a smaller cart', function () {
        $cart = fn () => pricedBy(cart(product(100, $this->pricing), product(50, $this->pricing)), $this->pricing);

        expect(product(100, $this->pricing)->getOSPriceInfo(2))
            ->getPrice()->getAmount()->toBeAmount(90)
            ->getTotalPrice()->getAmount()->toBeAmount(180)
            ->and($cart())->toCost(subTotal: 130, grandTotal: 130)
            ->and(withShipping($cart()))->toCost(subTotal: 130, grandTotal: 140);
    });

    it('applies both discounts to a cart that reaches the amount', function () {
        $cart = fn () => pricedBy(cart(product(100, $this->pricing), product(50, $this->pricing), product(80, $this->pricing)), $this->pricing);

        expect($cart())->toCost(subTotal: 200, grandTotal: 190)
            ->and(withShipping($cart()))->toCost(subTotal: 200, grandTotal: 200);
    });
});

dataset('rules with free shipping first or last', [
    'free shipping first' => [fn () => [
        rule(productDiscount(10)),
        rule(freeShipping(), cartAmountOfAtLeast(200)),
        rule(productDiscount(10), cartAmountOfAtLeast(10)),
    ]],
    'free shipping last' => [fn () => [
        rule(productDiscount(10)),
        rule(productDiscount(10), cartAmountOfAtLeast(10)),
        rule(freeShipping(), cartAmountOfAtLeast(200)),
    ]],
]);

it('charges shipping on a smaller cart, whatever the order of the rules', function (array $rules) {
    $pricing = pricingManager(...$rules);
    $cart = fn () => pricedBy(cart(product(100, $pricing), product(50, $pricing)), $pricing);

    expect(product(100, $pricing)->getOSPriceInfo(2))
        ->getPrice()->getAmount()->toBeAmount(90)
        ->getTotalPrice()->getAmount()->toBeAmount(180)
        ->and($cart())->toCost(subTotal: 130, grandTotal: 130)
        ->and(withShipping($cart()))->toCost(subTotal: 130, grandTotal: 140);
})->with('rules with free shipping first or last');

it('charges no shipping on a cart that reaches the amount, whatever the order of the rules', function (array $rules) {
    $pricing = pricingManager(...$rules);
    $cart = fn () => pricedBy(cart(product(100, $pricing), product(50, $pricing), product(80, $pricing)), $pricing);

    expect($cart())->toCost(subTotal: 200, grandTotal: 200)
        ->and(withShipping($cart()))->toCost(subTotal: 200, grandTotal: 200);
})->with('rules with free shipping first or last');

describe('a product discount for the products 4 and 5 together', function () {
    beforeEach(function () {
        $this->pricing = pricingManager(rule(productDiscount(10), allOf(catalogProduct(5), catalogProduct(4))));
        $this->cart = fn () => pricedBy(cart(product(100, $this->pricing, id: 4), product(50, $this->pricing, id: 5)), $this->pricing);
    });

    it('takes nothing off a single product, which is never both', function () {
        expect(product(100, $this->pricing, id: 4)->getOSPrice()->getAmount())->toBeAmount(100)
            ->and(product(50, $this->pricing, id: 5)->getOSPrice()->getAmount())->toBeAmount(50);
    });

    it('takes nothing off the products in a cart that holds both', function () {
        expect(($this->cart)())->toCost(subTotal: 150, grandTotal: 150)
            ->and(withShipping(($this->cart)()))->toCost(subTotal: 150, grandTotal: 160);
    });
});

describe('a product discount for the product 4 or 5', function () {
    beforeEach(function () {
        $this->pricing = pricingManager(rule(productDiscount(10), anyOf(catalogProduct(5), catalogProduct(4))));
        $this->cart = fn () => pricedBy(cart(product(100, $this->pricing, id: 4), product(50, $this->pricing, id: 5)), $this->pricing);
    });

    it('takes the discount off either product', function () {
        expect(product(100, $this->pricing, id: 4)->getOSPrice()->getAmount())->toBeAmount(90)
            ->and(product(50, $this->pricing, id: 5)->getOSPrice()->getAmount())->toBeAmount(40);
    });

    it('takes the discount off both products in a cart', function () {
        expect(($this->cart)())->toCost(subTotal: 130, grandTotal: 130)
            ->and(withShipping(($this->cart)()))->toCost(subTotal: 130, grandTotal: 140);
    });
});
