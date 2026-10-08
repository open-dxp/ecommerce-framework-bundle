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
