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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\Tests\Feature\Tax;

use OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem\TaxManagement\TaxEntry;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Tests\Application\CartManager\MockSessionCart;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Tests\Factory\ProductFactory;

function untaxedCart(): MockSessionCart
{
    $expensive = ProductFactory::new()
        ->costing(100)
        ->create();
    $cheap = ProductFactory::new()
        ->costing(50)
        ->create();

    $cart = cart();
    $cart->addItem($expensive, 2);
    $cart->addItem($cheap, 1);

    return $cart;
}

/**
 * @return MockSessionCart two of a product taxed with 10 and 15 percent and one taxed with 10 percent
 */
function taxedCart(string $calculationMode): MockSessionCart
{
    $twiceTaxed = ProductFactory::new()
        ->costing(100)
        ->taxedBy(
            [
                '1' => 10,
                '2' => 15,
            ],
            $calculationMode,
        )
        ->create();
    $onceTaxed = ProductFactory::new()
        ->costing(50)
        ->taxedBy(['1' => 10], $calculationMode)
        ->create();

    $cart = cart();
    $cart->addItem($twiceTaxed, 2);
    $cart->addItem($onceTaxed, 1);

    return $cart;
}

it('gives a cart without taxes the same net and gross totals', function () {
    $cart = untaxedCart();

    $calculator = $cart->getPriceCalculator();

    expect($cart->getItemAmount())
        ->toBe(3)
        ->and($calculator->getSubTotal())
        ->getGrossAmount()
        ->toBeAmount(250)
        ->getNetAmount()
        ->toBeAmount(250)
        ->and($calculator->getGrandTotal())
        ->getGrossAmount()
        ->toBeAmount(250)
        ->getNetAmount()
        ->toBeAmount(250);
});

it('adds an untaxed shipping charge to net and gross alike', function () {
    $cart = untaxedCart();

    $calculator = withShipping($cart)->getPriceCalculator();

    expect($calculator->getSubTotal())
        ->getGrossAmount()
        ->toBeAmount(250)
        ->getNetAmount()
        ->toBeAmount(250)
        ->and($calculator->getGrandTotal())
        ->getGrossAmount()
        ->toBeAmount(260)
        ->getNetAmount()
        ->toBeAmount(260);
});

dataset('calculation modes of the taxed cart', [
    'combined' => [TaxEntry::CALCULATION_MODE_COMBINE, '205.4545', '20.5455', '24'],
    'one after another' => [TaxEntry::CALCULATION_MODE_ONE_AFTER_ANOTHER, '203.5572', '20.3558', '26.087'],
]);

it('sums up the taxes of the items per tax', function (
    string $calculationMode,
    string $net,
    string $first,
    string $second,
) {
    $cart = taxedCart($calculationMode);

    $subTotal = $cart->getPriceCalculator()->getSubTotal();

    expect($subTotal)
        ->getGrossAmount()
        ->toBeAmount(250)
        ->getNetAmount()
        ->toBeAmount($net)
        ->and($subTotal->getTaxEntries()['1-10'])
        ->getPercent()
        ->toEqual(10)
        ->getAmount()
        ->toBeAmount($first)
        ->and($subTotal->getTaxEntries()['2-15'])
        ->getPercent()
        ->toEqual(15)
        ->getAmount()
        ->toBeAmount($second);
})->with('calculation modes of the taxed cart');

it('carries the taxes of the items into the grand total', function (
    string $calculationMode,
    string $net,
    string $first,
    string $second,
) {
    $cart = taxedCart($calculationMode);

    $grandTotal = $cart->getPriceCalculator()->getGrandTotal();

    expect($grandTotal)
        ->getGrossAmount()
        ->toBeAmount(250)
        ->getNetAmount()
        ->toBeAmount($net)
        ->and($grandTotal->getTaxEntries()['1-10']->getAmount())
        ->toBeAmount($first)
        ->and($grandTotal->getTaxEntries()['2-15']->getAmount())
        ->toBeAmount($second);
})->with('calculation modes of the taxed cart');

it('adds the tax of the shipping charge to the grand total', function (
    string $calculationMode,
    string $net,
    string $first,
    string $second,
) {
    $cart = taxedCart($calculationMode);

    $shippingTax = taxClass(['shipping' => 20], TaxEntry::CALCULATION_MODE_COMBINE);

    $grandTotal = withTaxedShipping($cart, $shippingTax)
        ->getPriceCalculator()
        ->getGrandTotal();

    expect($grandTotal)
        ->getGrossAmount()
        ->toBeAmount(260)
        ->getNetAmount()
        ->toBeAmount($net)
        ->and($grandTotal->getTaxEntries()['1-10']->getAmount())
        ->toBeAmount($first)
        ->and($grandTotal->getTaxEntries()['2-15']->getAmount())
        ->toBeAmount($second)
        ->and($grandTotal->getTaxEntries()['shipping-20'])
        ->getPercent()
        ->toEqual(20)
        ->getAmount()
        ->toBeAmount('1.6667');
})->with([
    'combined' => [TaxEntry::CALCULATION_MODE_COMBINE, '213.7878', '20.5455', '24'],
    'one after another' => [TaxEntry::CALCULATION_MODE_ONE_AFTER_ANOTHER, '211.8905', '20.3558', '26.087'],
]);
