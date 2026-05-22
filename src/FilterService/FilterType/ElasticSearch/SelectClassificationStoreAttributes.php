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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\FilterService\FilterType\ElasticSearch;

use OpenDxp\Bundle\EcommerceFrameworkBundle\FilterService\FilterType\AbstractFilterType;
use OpenDxp\Bundle\EcommerceFrameworkBundle\IndexService\ProductList\ProductListInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractFilterDefinitionType;
use OpenDxp\Model\DataObject\Classificationstore\KeyConfig;
use Override;

/**
 * @deprecated This class will be moved to the SearchIndex namespace in version 2.0.0.
 */
class SelectClassificationStoreAttributes extends AbstractFilterType
{
    /**
     * extract list of excluded keys from filter definition
     */
    protected function extractExcludedKeys(AbstractFilterDefinitionType $filterDefinition): array
    {
        $excludedKeys = [];

        if (method_exists($filterDefinition, 'getExcludedKeyIds') && $filterDefinition->getExcludedKeyIds()) {
            $excludedKeys = explode(',', (string) $filterDefinition->getExcludedKeyIds());
            $excludedKeys = array_map(intval(...), $excludedKeys);
        }

        return $excludedKeys;
    }

    #[Override]
    protected function sortResult(AbstractFilterDefinitionType $filterDefinition, array $keyCollection): array
    {
        if (!method_exists($filterDefinition, 'getKeyIdPriorityOrder') || empty($filterDefinition->getKeyIdPriorityOrder())) {
            return $keyCollection;
        }

        $priorityKeys = explode(',', (string) $filterDefinition->getKeyIdPriorityOrder());
        $priorityKeys = array_map(intval(...), $priorityKeys);

        $sortedCollection = [];

        foreach ($priorityKeys as $key) {
            $sortedCollection[$key] = $keyCollection[$key];
            unset($keyCollection[$key]);
        }

        return $sortedCollection + $keyCollection;
    }

    #[Override]
    public function prepareGroupByValues(AbstractFilterDefinitionType $filterDefinition, ProductListInterface $productList): void
    {
        $field = $this->getField($filterDefinition);
        $keysField = $field . '.keys';
        $valuesField = $field . '.values';

        $productList->prepareGroupByValues($keysField, false, true);
        $values = $productList->getGroupByValues($keysField, false, false);

        $excludedKeys = $this->extractExcludedKeys($filterDefinition);
        foreach ($values as $keyId) {
            if (in_array($keyId, $excludedKeys)) {
                continue;
            }

            $subField = $valuesField . '.' . $keyId . '.keyword';
            $productList->prepareGroupByValues($subField, false, true);
        }
    }

    public function getFilterValues(AbstractFilterDefinitionType $filterDefinition, ProductListInterface $productList, array $currentFilter): array
    {
        $field = $this->getField($filterDefinition);
        $keysField = $field . '.keys';

        $keys = $productList->getGroupByValues($keysField, false, false);

        $keyCollection = [];

        $excludedKeys = $this->extractExcludedKeys($filterDefinition);
        foreach ($keys as $keyId) {
            if (in_array($keyId, $excludedKeys)) {
                continue;
            }

            $valuesField = $field . '.values.' . $keyId . '.keyword';

            $keyValues = $productList->getGroupByValues($valuesField, true, true);
            if ($keyValues !== []) {
                $key = KeyConfig::getById($keyId);

                $keyCollection[$keyId] = [
                    'keyConfig' => $key,
                    'values' => $keyValues,
                ];
            }
        }

        $keyCollection = $this->sortResult($filterDefinition, $keyCollection);

        return [
            'label' => $filterDefinition->getLabel(),
            'fieldname' => $field,
            'currentValue' => $currentFilter[$field] ?? null,
            'values' => $keyCollection,
            'metaData' => $filterDefinition->getMetaData(),
        ];
    }

    public function addCondition(AbstractFilterDefinitionType $filterDefinition, ProductListInterface $productList, array $currentFilter, array $params, bool $isPrecondition = false): array
    {
        $field = $this->getField($filterDefinition);
        $nestedPath = $field . '.values';

        $value = $params[$field] ?? null;

        if (is_array($value)) {
            foreach ($value as $keyId => $keyValue) {
                $filterValue = trim((string) $keyValue);
                if ($filterValue === AbstractFilterType::EMPTY_STRING) {
                    $filterValue = null;
                }

                if ($filterValue) {
                    $currentFilter[$field][$keyId] = $filterValue;

                    $valueField = $nestedPath . '.' . $keyId . '.keyword';
                    $productList->addCondition($filterValue, $valueField);
                }
            }
        }

        return $currentFilter;
    }
}
