<?php

declare(strict_types=1);

it('takes a product discount off every product', function () {
    $pricing = pricingManager(rule(productDiscount(10)));
    $cart = fn () => pricedBy(cart(product(100, $pricing)), $pricing);

    expect(product(100, $pricing)->getOSPriceInfo(2))
        ->getPrice()->getAmount()->toBeAmount(90)
        ->getTotalPrice()->getAmount()->toBeAmount(180)
        ->and($cart())->toCost(subTotal: 90, grandTotal: 90)
        ->and(withShipping($cart()))->toCost(subTotal: 90, grandTotal: 100);
});

it('takes a cart discount off the total of the cart', function () {
    $pricing = pricingManager(rule(cartDiscount(10)));
    $cart = fn () => pricedBy(cart(product(100, $pricing), product(40, $pricing)), $pricing);

    expect(product(100, $pricing)->getOSPriceInfo(2))
        ->getPrice()->getAmount()->toBeAmount(100)
        ->getTotalPrice()->getAmount()->toBeAmount(200)
        ->and($cart())->toCost(subTotal: 140, grandTotal: 130)
        ->and(withShipping($cart()))->toCost(subTotal: 140, grandTotal: 140);
});

it('takes no cart discount off a cart below the amount the condition asks for', function () {
    $pricing = pricingManager(rule(cartDiscount(10), allOf(cartAmountOfAtLeast(200))));
    $cart = fn () => pricedBy(cart(product(100, $pricing), product(40, $pricing)), $pricing);

    expect(product(100, $pricing)->getOSPriceInfo(2))
        ->getPrice()->getAmount()->toBeAmount(100)
        ->getTotalPrice()->getAmount()->toBeAmount(200)
        ->and($cart())->toCost(subTotal: 140, grandTotal: 140)
        ->and(withShipping($cart()))->toCost(subTotal: 140, grandTotal: 150);
});

it('takes a cart discount off a cart that reaches the amount the condition asks for', function () {
    $pricing = pricingManager(rule(cartDiscount(10), cartAmountOfAtLeast(200)));
    $cart = fn () => pricedBy(cart(product(200, $pricing), product(40, $pricing)), $pricing);

    expect(product(100, $pricing)->getOSPriceInfo(2))
        ->getPrice()->getAmount()->toBeAmount(100)
        ->getTotalPrice()->getAmount()->toBeAmount(200)
        ->and($cart())->toCost(subTotal: 240, grandTotal: 230)
        ->and(withShipping($cart()))->toCost(subTotal: 240, grandTotal: 240);
});

it('takes a product and a cart discount of the same rule', function () {
    $pricing = pricingManager(rule([cartDiscount(10), productDiscount(15)]));
    $cart = fn () => pricedBy(cart(product(100, $pricing), product(40, $pricing)), $pricing);

    expect(product(100, $pricing)->getOSPriceInfo(2))
        ->getPrice()->getAmount()->toBeAmount(85)
        ->getTotalPrice()->getAmount()->toBeAmount(170)
        ->and($cart())->toCost(subTotal: 110, grandTotal: 100)
        ->and(withShipping($cart()))->toCost(subTotal: 110, grandTotal: 110);
});

describe('a gift for carts from 200 euros', function () {
    beforeEach(function () {
        $this->pricing = pricingManager(rule(gift(product(100)), cartAmountOfAtLeast(200)));
    });

    it('adds no gift to a smaller cart', function () {
        $cart = withShipping(pricedBy(cart(product(100, $this->pricing), product(40, $this->pricing)), $this->pricing));

        expect($cart)->toCost(subTotal: 140, grandTotal: 150)
            ->and($cart->getGiftItems())->toBeEmpty();
    });

    it('adds the gift to a cart that reaches the amount', function () {
        $cart = withShipping(pricedBy(cart(product(100, $this->pricing), product(40, $this->pricing), product(80, $this->pricing)), $this->pricing));

        expect($cart)->toCost(subTotal: 220, grandTotal: 230)
            ->and($cart->getGiftItems())->toHaveCount(1);
    });
});

it('adds every gift of a rule', function () {
    $pricing = pricingManager(rule([gift(product(100)), gift(product(200))], cartAmountOfAtLeast(200)));
    $cart = withShipping(pricedBy(cart(product(100, $pricing), product(40, $pricing), product(80, $pricing)), $pricing));

    expect($cart)->toCost(subTotal: 220, grandTotal: 230)
        ->and($cart->getGiftItems())->toHaveCount(2);
});

describe('free shipping for carts from 200 euros', function () {
    beforeEach(function () {
        $this->pricing = pricingManager(rule(freeShipping(), cartAmountOfAtLeast(200)));
    });

    it('charges shipping on a smaller cart', function () {
        $cart = withShipping(pricedBy(cart(product(100, $this->pricing), product(40, $this->pricing)), $this->pricing));

        expect($cart)->toCost(subTotal: 140, grandTotal: 150)
            ->and($cart->getPriceCalculator()->getPriceModifications()['shipping']->getAmount())->toBeAmount(10);
    });

    it('charges no shipping on a cart that reaches the amount', function () {
        $cart = withShipping(pricedBy(cart(product(100, $this->pricing), product(40, $this->pricing), product(80, $this->pricing)), $this->pricing));

        expect($cart)->toCost(subTotal: 220, grandTotal: 220)
            ->and($cart->getPriceCalculator()->getPriceModifications()['shipping']->getAmount())->toBeAmount(0);
    });
});
