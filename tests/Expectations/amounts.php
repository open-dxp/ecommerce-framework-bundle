<?php

declare(strict_types=1);

use OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem\PriceInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem\TaxManagement\TaxEntry;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Type\Decimal;

expect()->extend('toBeAmount', function (float|int|string $expected) {
    expect($this->value)
        ->toBeInstanceOf(Decimal::class)
        ->and($this->value->asString())
        ->toBe(Decimal::create($expected)->asString());

    return $this;
});

expect()->extend('toCost', function (float|int $subTotal, float|int $grandTotal) {
    expect($this->value)->toBeInstanceOf(CartInterface::class);

    $calculator = $this->value->getPriceCalculator();

    expect($calculator->getSubTotal()->getAmount())
        ->toBeAmount($subTotal)
        ->and($calculator->getGrandTotal()->getAmount())
        ->toBeAmount($grandTotal);

    return $this;
});

expect()->extend('toAddUpToItsGrossAmount', function () {
    expect($this->value)->toBeInstanceOf(PriceInterface::class);

    $sum = array_reduce(
        $this->value->getTaxEntries(),
        static fn (Decimal $sum, TaxEntry $entry): Decimal => $sum->add($entry->getAmount()),
        $this->value->getNetAmount(),
    );

    expect($sum->asString())->toBe($this->value->getGrossAmount()->asString());

    return $this;
});
