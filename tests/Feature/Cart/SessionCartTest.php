<?php

declare(strict_types=1);

use OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractSetProductEntry;

it('counts one item for a product added twice', function () {
    $cart = cart();
    $cart->addItem(product(0), 2);

    expect($cart->getItems())->toHaveCount(1)
        ->and($cart->getItemAmount())->toBe(2)
        ->and($cart->getItemCount())->toBe(1);
});

it('counts the items of a set in the way it is asked to', function (string $mode, int $amount, int $count) {
    $cart = cart();
    $cart->addItem(product(0), 2, null, false, [], [new AbstractSetProductEntry(product(0), 1), new AbstractSetProductEntry(product(0), 2)]);
    $cart->addItem(product(0), 6);

    expect($cart->getItems())->toHaveCount(2)
        ->and($cart->getItemAmount($mode))->toBe($amount)
        ->and($cart->getItemCount($mode))->toBe($count);
})->with([
    'the main items only' => [CartInterface::COUNT_MAIN_ITEMS_ONLY, 8, 2],
    'the main and the sub items' => [CartInterface::COUNT_MAIN_AND_SUB_ITEMS, 14, 4],
    'the sub items instead of their main item' => [CartInterface::COUNT_MAIN_OR_SUB_ITEMS, 12, 3],
]);

it('counts the main items by default', function () {
    $cart = cart();
    $cart->addItem(product(0), 2, null, false, [], [new AbstractSetProductEntry(product(0), 1)]);

    expect($cart->getItemAmount())->toBe($cart->getItemAmount(CartInterface::COUNT_MAIN_ITEMS_ONLY))
        ->and($cart->getItemCount())->toBe($cart->getItemCount(CartInterface::COUNT_MAIN_ITEMS_ONLY));
});
