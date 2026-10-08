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
use OpenDxp\Bundle\EcommerceFrameworkBundle\Tests\Factory\ProductFactory;

it('gives a product without taxes the same net and gross price', function () {
    $product = ProductFactory::new()
        ->costing(100)
        ->create();

    $price = $product->getOSPrice();

    expect($price)
        ->getAmount()
        ->toBeAmount(100)
        ->getNetAmount()
        ->toBeAmount(100)
        ->getGrossAmount()
        ->toBeAmount(100);
});

it('takes the taxes out of the gross price of a product', function (string $calculationMode, string $net) {
    $product = ProductFactory::new()
        ->costing(100)
        ->taxedBy(
            [
                'tax_1' => 10,
                'tax_2' => 15,
            ],
            $calculationMode,
        )
        ->create();

    $price = $product->getOSPrice();

    expect($price)
        ->getGrossAmount()
        ->toBeAmount(100)
        ->getNetAmount()
        ->toBeAmount($net);
})->with([
    'combined' => [TaxEntry::CALCULATION_MODE_COMBINE, '80'],
    'one after another' => [TaxEntry::CALCULATION_MODE_ONE_AFTER_ANOTHER, '79.0514'],
]);
