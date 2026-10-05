<?php

declare(strict_types=1);

use OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem\TaxManagement\TaxEntry;

it('gives a cart without taxes the same net and gross totals', function () {
    $cart = cart();
    $cart->addItem(product(100), 2);
    $cart->addItem(product(50), 1);
    $calculator = $cart->getPriceCalculator();

    expect($cart->getItemAmount())->toBe(3)
        ->and($calculator->getSubTotal())->getGrossAmount()->toBeAmount(250)->getNetAmount()->toBeAmount(250)
        ->and($calculator->getGrandTotal())->getGrossAmount()->toBeAmount(250)->getNetAmount()->toBeAmount(250);
});

it('adds an untaxed shipping charge to net and gross alike', function () {
    $cart = cart();
    $cart->addItem(product(100), 2);
    $cart->addItem(product(50), 1);
    $calculator = withShipping($cart)->getPriceCalculator();

    expect($calculator->getSubTotal())->getGrossAmount()->toBeAmount(250)->getNetAmount()->toBeAmount(250)
        ->and($calculator->getGrandTotal())->getGrossAmount()->toBeAmount(260)->getNetAmount()->toBeAmount(260);
});

describe('two of a product taxed with 10 and 15 percent and one taxed with 10 percent', function () {
    it('sums up the taxes of the items per tax', function (string $calculationMode, string $net, string $firstTax, string $secondTax) {
        $cart = cart();
        $cart->addItem(product(100, taxes: ['1' => 10, '2' => 15], calculationMode: $calculationMode), 2);
        $cart->addItem(product(50, taxes: ['1' => 10], calculationMode: $calculationMode), 1);
        $subTotal = $cart->getPriceCalculator()->getSubTotal();
        $grandTotal = $cart->getPriceCalculator()->getGrandTotal();

        expect($subTotal)->getGrossAmount()->toBeAmount(250)->getNetAmount()->toBeAmount($net)
            ->and($subTotal->getTaxEntries()['1-10'])->getPercent()->toEqual(10)->getAmount()->toBeAmount($firstTax)
            ->and($subTotal->getTaxEntries()['2-15'])->getPercent()->toEqual(15)->getAmount()->toBeAmount($secondTax)
            ->and($grandTotal)->getGrossAmount()->toBeAmount(250)->getNetAmount()->toBeAmount($net)
            ->and($grandTotal->getTaxEntries()['1-10'])->getAmount()->toBeAmount($firstTax)
            ->and($grandTotal->getTaxEntries()['2-15'])->getAmount()->toBeAmount($secondTax);
    })->with([
        'combined' => [TaxEntry::CALCULATION_MODE_COMBINE, '205.4545', '20.5455', '24'],
        'one after another' => [TaxEntry::CALCULATION_MODE_ONE_AFTER_ANOTHER, '203.5572', '20.3558', '26.087'],
    ]);

    it('adds the tax of the shipping charge to the grand total', function (string $calculationMode, string $net, string $firstTax, string $secondTax) {
        $cart = cart();
        $cart->addItem(product(100, taxes: ['1' => 10, '2' => 15], calculationMode: $calculationMode), 2);
        $cart->addItem(product(50, taxes: ['1' => 10], calculationMode: $calculationMode), 1);
        $grandTotal = withShipping($cart, taxClass(['shipping' => 20]))->getPriceCalculator()->getGrandTotal();

        expect($grandTotal)->getGrossAmount()->toBeAmount(260)->getNetAmount()->toBeAmount($net)
            ->and($grandTotal->getTaxEntries()['1-10'])->getAmount()->toBeAmount($firstTax)
            ->and($grandTotal->getTaxEntries()['2-15'])->getAmount()->toBeAmount($secondTax)
            ->and($grandTotal->getTaxEntries()['shipping-20'])->getPercent()->toEqual(20)->getAmount()->toBeAmount('1.6667');
    })->with([
        'combined' => [TaxEntry::CALCULATION_MODE_COMBINE, '213.7878', '20.5455', '24'],
        'one after another' => [TaxEntry::CALCULATION_MODE_ONE_AFTER_ANOTHER, '211.8905', '20.3558', '26.087'],
    ]);
});
