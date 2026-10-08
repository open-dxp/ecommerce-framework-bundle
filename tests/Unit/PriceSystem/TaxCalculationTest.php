<?php

declare(strict_types=1);

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\Tests\Unit\PriceSystem;

use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\Currency;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem\Price;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem\PriceInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem\TaxManagement\TaxCalculationService;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem\TaxManagement\TaxEntry;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Type\Decimal;

const TWELVE_AND_FOUR_PERCENT = [
    12,
    4,
];

/**
 * @param list<float|int> $percents
 */
function priceInEuros(float|int $amount, array $percents, string $calculationMode): Price
{
    $price = new Price(Decimal::create($amount), new Currency('EUR'));
    $price->setTaxEntryCombinationMode($calculationMode);
    $price->setTaxEntries(array_map(
        static fn (float|int $percent): TaxEntry => new TaxEntry($percent, Decimal::create(0)),
        $percents,
    ));

    return $price;
}

describe('a price without taxes', function () {
    beforeEach(function () {
        $this->price = priceInEuros(100, [], TaxEntry::CALCULATION_MODE_COMBINE);
    });

    it('starts with the same net and gross amount', function () {
        expect($this->price)
            ->getAmount()
            ->toBeAmount(100)
            ->getGrossAmount()
            ->toBeAmount(100)
            ->getNetAmount()
            ->toBeAmount(100);
    });

    it('keeps the gross amount when the net amount is set without recalculation', function () {
        $this->price->setNetAmount(Decimal::create(90));

        expect($this->price)
            ->getGrossAmount()
            ->toBeAmount(100)
            ->getNetAmount()
            ->toBeAmount(90);
    });

    it('takes the net amount for the gross amount when the taxes are calculated from net', function () {
        $this->price->setNetAmount(Decimal::create(90), false);

        (new TaxCalculationService())->updateTaxes($this->price, TaxCalculationService::CALCULATION_FROM_NET);

        expect($this->price)
            ->getGrossAmount()
            ->toBeAmount(90)
            ->getNetAmount()
            ->toBeAmount(90);
    });

    it('makes the gross amount the net amount when the net amount is set with recalculation', function () {
        $this->price->setNetAmount(Decimal::create(90), true);

        expect($this->price)
            ->getGrossAmount()
            ->toBeAmount(90)
            ->getNetAmount()
            ->toBeAmount(90);
    });

    it('makes the net amount the gross amount when the gross amount is set with recalculation', function () {
        $this->price->setGrossAmount(Decimal::create(110), true);

        expect($this->price)
            ->getGrossAmount()
            ->toBeAmount(110)
            ->getNetAmount()
            ->toBeAmount(110);
    });

    it('sets the amount of the given mode and recalculates the other one only when asked to', function (
        int $amount,
        string $mode,
        bool $recalculate,
        int $gross,
        int $net,
    ) {
        $this->price->setAmount(Decimal::create($amount), $mode, $recalculate);

        expect($this->price)
            ->getGrossAmount()
            ->toBeAmount($gross)
            ->getNetAmount()
            ->toBeAmount($net);
    })->with([
        'gross without recalculation' => [110, PriceInterface::PRICE_MODE_GROSS, false, 110, 100],
        'gross with recalculation' => [120, PriceInterface::PRICE_MODE_GROSS, true, 120, 120],
        'net without recalculation' => [90, PriceInterface::PRICE_MODE_NET, false, 100, 90],
        'net with recalculation' => [80, PriceInterface::PRICE_MODE_NET, true, 80, 80],
    ]);
});

describe('a price with a single tax of 10 percent', function () {
    it('adds the tax to the net amount', function () {
        $price = priceInEuros(90, [10], TaxEntry::CALCULATION_MODE_COMBINE);

        (new TaxCalculationService())->updateTaxes($price, TaxCalculationService::CALCULATION_FROM_NET);

        expect($price)
            ->getGrossAmount()
            ->toBeAmount(99)
            ->getTaxEntries()
            ->toHaveCount(1)
            ->and($price->getTaxEntries()[0]->getAmount())
            ->toBeAmount(9);
    });

    it('takes the tax out of the gross amount', function () {
        $price = priceInEuros(100, [10], TaxEntry::CALCULATION_MODE_COMBINE);

        (new TaxCalculationService())->updateTaxes($price, TaxCalculationService::CALCULATION_FROM_GROSS);

        expect($price)
            ->getNetAmount()
            ->toBeAmount('90.9091')
            ->and($price)
            ->toAddUpToItsGrossAmount();
    });
});

describe('a price with a single tax of 15 percent', function () {
    beforeEach(function () {
        $this->price = priceInEuros(0, [15], TaxEntry::CALCULATION_MODE_COMBINE);
    });

    it('recalculates the tax when the gross amount is set with recalculation', function () {
        $this->price->setGrossAmount(Decimal::create(110), true);

        expect($this->price)
            ->getGrossAmount()
            ->toBeAmount(110)
            ->getNetAmount()
            ->toBeAmount('95.6522')
            ->and($this->price->getTaxEntries()[0]->getAmount())
            ->toBeAmount('14.3478')
            ->and($this->price)
            ->toAddUpToItsGrossAmount();
    });

    it('recalculates the tax when the net amount is set with recalculation', function () {
        $this->price->setNetAmount(Decimal::create(100), true);

        expect($this->price)
            ->getGrossAmount()
            ->toBeAmount(115)
            ->and($this->price->getTaxEntries()[0]->getAmount())
            ->toBeAmount(15);
    });
});

describe('a price with taxes of 12 and 4 percent', function () {
    it('adds the taxes to the net amount one after another', function () {
        $price = priceInEuros(90, TWELVE_AND_FOUR_PERCENT, TaxEntry::CALCULATION_MODE_ONE_AFTER_ANOTHER);

        (new TaxCalculationService())->updateTaxes($price);

        expect($price)
            ->getGrossAmount()
            ->toBeAmount('104.832')
            ->and($price->getTaxEntries()[0]->getAmount())
            ->toBeAmount('10.8')
            ->and($price->getTaxEntries()[1]->getAmount())
            ->toBeAmount('4.032')
            ->and($price)
            ->toAddUpToItsGrossAmount();
    });

    it('adds the combined taxes to the net amount', function () {
        $price = priceInEuros(90, TWELVE_AND_FOUR_PERCENT, TaxEntry::CALCULATION_MODE_COMBINE);

        (new TaxCalculationService())->updateTaxes($price);

        expect($price)
            ->getGrossAmount()
            ->toBeAmount('104.4')
            ->and($price->getTaxEntries()[0]->getAmount())
            ->toBeAmount('10.8')
            ->and($price->getTaxEntries()[1]->getAmount())
            ->toBeAmount('3.6')
            ->and($price)
            ->toAddUpToItsGrossAmount();
    });

    it('takes the combined taxes out of the gross amount', function () {
        $price = priceInEuros(100, TWELVE_AND_FOUR_PERCENT, TaxEntry::CALCULATION_MODE_COMBINE);

        (new TaxCalculationService())->updateTaxes($price, TaxCalculationService::CALCULATION_FROM_GROSS);

        expect($price)
            ->getNetAmount()
            ->toBeAmount('86.2069')
            ->and($price->getTaxEntries()[0]->getAmount())
            ->toBeAmount('10.3448')
            ->and($price->getTaxEntries()[1]->getAmount())
            ->toBeAmount('3.4483')
            ->and($price)
            ->toAddUpToItsGrossAmount();
    });
});
