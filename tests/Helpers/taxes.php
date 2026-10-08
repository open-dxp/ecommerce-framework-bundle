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

use OpenDxp\Model\DataObject\Fieldcollection;
use OpenDxp\Model\DataObject\Fieldcollection\Data\TaxEntry as TaxEntryData;
use OpenDxp\Model\DataObject\OnlineShopTaxClass;

/**
 * @param array<string, float|int> $taxes percent by tax name
 */
function taxClass(array $taxes, string $calculationMode): OnlineShopTaxClass
{
    $entries = new Fieldcollection();

    foreach ($taxes as $name => $percent) {
        $entry = new TaxEntryData();
        $entry->setName((string) $name);
        $entry->setPercent($percent);
        $entries->add($entry);
    }

    $taxClass = new OnlineShopTaxClass();
    $taxClass->setTaxEntries($entries);
    $taxClass->setTaxEntryCombinationType($calculationMode);

    return $taxClass;
}
