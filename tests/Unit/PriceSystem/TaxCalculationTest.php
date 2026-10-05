<?php

declare(strict_types=1);

use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\Currency;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem\Price;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem\PriceInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem\TaxManagement\TaxCalculationService;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem\TaxManagement\TaxEntry;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Type\Decimal;

describe('a price without taxes', function () {
    it('starts with the same net and gross amount', function () {
        expect(new Price(Decimal::create(100), new Currency('EUR')))
            ->getAmount()->toBeAmount(100)
            ->getGrossAmount()->toBeAmount(100)
            ->getNetAmount()->toBeAmount(100);
    });

    it('keeps the gross amount when the net amount is set without recalculation', function () {
        $price = new Price(Decimal::create(100), new Currency('EUR'));
        $price->setNetAmount(Decimal::create(90));

        expect($price)->getGrossAmount()->toBeAmount(100)->getNetAmount()->toBeAmount(90);
    });

    it('takes the net amount for the gross amount when the taxes are calculated from net', function () {
        $price = new Price(Decimal::create(100), new Currency('EUR'));
        $price->setNetAmount(Decimal::create(90), false);

        (new TaxCalculationService())->updateTaxes($price, TaxCalculationService::CALCULATION_FROM_NET);

        expect($price)->getGrossAmount()->toBeAmount(90)->getNetAmount()->toBeAmount(90);
    });

    it('makes net and gross the same when an amount is set with recalculation', function () {
        $price = new Price(Decimal::create(100), new Currency('EUR'));

        $price->setNetAmount(Decimal::create(90), true);
        expect($price)->getGrossAmount()->toBeAmount(90)->getNetAmount()->toBeAmount(90);

        $price->setGrossAmount(Decimal::create(110), true);
        expect($price)->getGrossAmount()->toBeAmount(110)->getNetAmount()->toBeAmount(110);
    });

    it('sets the amount of the given mode and recalculates the other one only when asked to', function () {
        $price = new Price(Decimal::create(100), new Currency('EUR'));

        $price->setAmount(Decimal::create(110), PriceInterface::PRICE_MODE_GROSS, false);
        expect($price)->getGrossAmount()->toBeAmount(110)->getNetAmount()->toBeAmount(100);

        $price->setAmount(Decimal::create(120), PriceInterface::PRICE_MODE_GROSS, true);
        expect($price)->getGrossAmount()->toBeAmount(120)->getNetAmount()->toBeAmount(120);

        $price->setAmount(Decimal::create(90), PriceInterface::PRICE_MODE_NET, false);
        expect($price)->getGrossAmount()->toBeAmount(120)->getNetAmount()->toBeAmount(90);

        $price->setAmount(Decimal::create(80), PriceInterface::PRICE_MODE_NET, true);
        expect($price)->getGrossAmount()->toBeAmount(80)->getNetAmount()->toBeAmount(80);
    });
});

describe('a price with a single tax', function () {
    it('adds the tax to the net amount and takes it out of the gross amount', function () {
        $price = new Price(Decimal::create(90), new Currency('EUR'));
        $price->setTaxEntries([new TaxEntry(10, Decimal::create(0))]);
        $calculation = new TaxCalculationService();

        $calculation->updateTaxes($price, TaxCalculationService::CALCULATION_FROM_NET);
        expect($price)->getGrossAmount()->toBeAmount(99)
            ->and($price->getTaxEntries())->toHaveCount(1)
            ->and($price->getTaxEntries()[0]->getAmount())->toBeAmount(9);

        $price->setGrossAmount(Decimal::create(100));
        $calculation->updateTaxes($price, TaxCalculationService::CALCULATION_FROM_GROSS);
        expect($price)->getNetAmount()->toBeAmount('90.9091')
            ->and($price)->toAddUpToItsGrossAmount();
    });

    it('recalculates the tax when an amount is set with recalculation', function () {
        $price = new Price(Decimal::create(0), new Currency('EUR'));
        $price->setTaxEntries([new TaxEntry(15, Decimal::create(0))]);

        $price->setGrossAmount(Decimal::create(110), true);
        expect($price)->getGrossAmount()->toBeAmount(110)->getNetAmount()->toBeAmount('95.6522')
            ->and($price->getTaxEntries())->toHaveCount(1)
            ->and($price->getTaxEntries()[0]->getAmount())->toBeAmount('14.3478')
            ->and($price)->toAddUpToItsGrossAmount();

        $price->setNetAmount(Decimal::create(100), true);
        expect($price)->getGrossAmount()->toBeAmount(115)
            ->and($price->getTaxEntries()[0]->getAmount())->toBeAmount(15);
    });
});

describe('a price with taxes of 12 and 4 percent', function () {
    it('adds the taxes to the net amount one after another', function () {
        $price = new Price(Decimal::create(90), new Currency('EUR'));
        $price->setTaxEntryCombinationMode(TaxEntry::CALCULATION_MODE_ONE_AFTER_ANOTHER);
        $price->setTaxEntries([new TaxEntry(12, Decimal::create(0)), new TaxEntry(4, Decimal::create(0))]);

        (new TaxCalculationService())->updateTaxes($price);

        expect($price)->getGrossAmount()->toBeAmount('104.832')
            ->and($price->getTaxEntries())->toHaveCount(2)
            ->and($price->getTaxEntries()[0]->getAmount())->toBeAmount('10.8')
            ->and($price->getTaxEntries()[1]->getAmount())->toBeAmount('4.032')
            ->and($price)->toAddUpToItsGrossAmount();
    });

    it('adds the combined taxes to the net amount', function () {
        $price = new Price(Decimal::create(90), new Currency('EUR'));
        $price->setTaxEntryCombinationMode(TaxEntry::CALCULATION_MODE_COMBINE);
        $price->setTaxEntries([new TaxEntry(12, Decimal::create(0)), new TaxEntry(4, Decimal::create(0))]);

        (new TaxCalculationService())->updateTaxes($price);

        expect($price)->getGrossAmount()->toBeAmount('104.4')
            ->and($price->getTaxEntries())->toHaveCount(2)
            ->and($price->getTaxEntries()[0]->getAmount())->toBeAmount('10.8')
            ->and($price->getTaxEntries()[1]->getAmount())->toBeAmount('3.6')
            ->and($price)->toAddUpToItsGrossAmount();
    });

    it('takes the combined taxes out of the gross amount', function () {
        $price = new Price(Decimal::create(100), new Currency('EUR'));
        $price->setTaxEntryCombinationMode(TaxEntry::CALCULATION_MODE_COMBINE);
        $price->setTaxEntries([new TaxEntry(12, Decimal::create(0)), new TaxEntry(4, Decimal::create(0))]);

        (new TaxCalculationService())->updateTaxes($price, TaxCalculationService::CALCULATION_FROM_GROSS);

        expect($price)->getNetAmount()->toBeAmount('86.2069')
            ->and($price->getTaxEntries())->toHaveCount(2)
            ->and($price->getTaxEntries()[0]->getAmount())->toBeAmount('10.3448')
            ->and($price->getTaxEntries()[1]->getAmount())->toBeAmount('3.4483')
            ->and($price)->toAddUpToItsGrossAmount();
    });
});
