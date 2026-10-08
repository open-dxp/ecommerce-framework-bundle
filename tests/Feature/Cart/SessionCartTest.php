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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\Tests\Feature\Cart;

use OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractSetProductEntry;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Tests\Factory\ProductFactory;

it('counts one item for a product added twice', function () {
    $cart = cart();

    $cart->addItem(ProductFactory::createOne(), 2);

    expect($cart)
        ->getItems()
        ->toHaveCount(1)
        ->getItemAmount()
        ->toBe(2)
        ->getItemCount()
        ->toBe(1);
});

it('counts the items of a set in the way it is asked to', function (string $mode, int $amount, int $count) {
    $cart = cart();
    $subItems = [
        new AbstractSetProductEntry(ProductFactory::createOne(), 1),
        new AbstractSetProductEntry(ProductFactory::createOne(), 2),
    ];
    $cart->addItem(ProductFactory::createOne(), 2, null, false, [], $subItems);

    $cart->addItem(ProductFactory::createOne(), 6);

    expect($cart->getItems())
        ->toHaveCount(2)
        ->and($cart->getItemAmount($mode))
        ->toBe($amount)
        ->and($cart->getItemCount($mode))
        ->toBe($count);
})->with([
    'the main items only' => [CartInterface::COUNT_MAIN_ITEMS_ONLY, 8, 2],
    'the main and the sub items' => [CartInterface::COUNT_MAIN_AND_SUB_ITEMS, 14, 4],
    'the sub items instead of their main item' => [CartInterface::COUNT_MAIN_OR_SUB_ITEMS, 12, 3],
]);

it('counts the main items by default', function () {
    $cart = cart();
    $subItems = [new AbstractSetProductEntry(ProductFactory::createOne(), 1)];

    $cart->addItem(ProductFactory::createOne(), 2, null, false, [], $subItems);

    expect($cart->getItemAmount())
        ->toBe($cart->getItemAmount(CartInterface::COUNT_MAIN_ITEMS_ONLY))
        ->and($cart->getItemCount())
        ->toBe($cart->getItemCount(CartInterface::COUNT_MAIN_ITEMS_ONLY));
});
