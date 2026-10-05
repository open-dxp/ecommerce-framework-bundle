<?php

declare(strict_types=1);

use OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem\TaxManagement\TaxEntry;

it('gives a product without taxes the same net and gross price', function () {
    expect(product(100)->getOSPrice())
        ->getAmount()->toBeAmount(100)
        ->getNetAmount()->toBeAmount(100)
        ->getGrossAmount()->toBeAmount(100);
});

it('takes the taxes out of the gross price of a product', function (string $calculationMode, string $net) {
    expect(product(100, taxes: ['tax_1' => 10, 'tax_2' => 15], calculationMode: $calculationMode)->getOSPrice())
        ->getGrossAmount()->toBeAmount(100)
        ->getNetAmount()->toBeAmount($net);
})->with([
    'combined' => [TaxEntry::CALCULATION_MODE_COMBINE, '80'],
    'one after another' => [TaxEntry::CALCULATION_MODE_ONE_AFTER_ANOTHER, '79.0514'],
]);
