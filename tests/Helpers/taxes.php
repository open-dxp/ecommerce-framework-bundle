<?php

declare(strict_types=1);

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
