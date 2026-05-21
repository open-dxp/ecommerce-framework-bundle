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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\FilterService\FilterType;

use OpenDxp\Bundle\EcommerceFrameworkBundle\Exception\InvalidConfigException;
use OpenDxp\Bundle\EcommerceFrameworkBundle\IndexService\ProductList\ProductListInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractFilterDefinitionType;
use OpenDxp\Db;
use OpenDxp\Model\DataObject\Fieldcollection\Data\FilterCategoryMultiselect;
use OpenDxp\Model\Element\ElementInterface;

class MultiSelectCategory extends AbstractFilterType
{
    public function getFilterValues(AbstractFilterDefinitionType $filterDefinition, ProductListInterface $productList, array $currentFilter): array
    {
        $rawValues = $productList->getGroupByValues($filterDefinition->getField(), true);
        $values = [];

        /** @var array<string, bool> $availableRelations */
        $availableRelations = [];
        if (!$filterDefinition instanceof FilterCategoryMultiselect) {
            throw new InvalidConfigException('invalid configuration');
        }

        if (method_exists($filterDefinition, 'getAvailableCategories') && $filterDefinition->getAvailableCategories()) {
            /** @var ElementInterface $rel */
            foreach ($filterDefinition->getAvailableCategories() as $rel) {
                $availableRelations[$rel->getId()] = true;
            }
        }

        foreach ($rawValues as $v) {
            if ($v['value']) {
                $explode = array_map(intval(...), explode(',', (string) $v['value']));
                foreach ($explode as $e) {
                    if (empty($availableRelations) || ($availableRelations[$e] ?? false)) {
                        $count = empty($values[$e]) ? $v['count'] : $values[$e]['count'] + $v['count'];
                        $values[$e] = ['value' => $e, 'count' => $count];
                    }
                }
            }
        }

        return [
            'hideFilter' => $filterDefinition->getRequiredFilterField() && empty($currentFilter[$filterDefinition->getRequiredFilterField()]),
            'label' => $filterDefinition->getLabel(),
            'currentValue' => $currentFilter[$filterDefinition->getField()],
            'values' => array_values($values),
            'fieldname' => $filterDefinition->getField(),
            'metaData' => $filterDefinition->getMetaData(),
            'resultCount' => $productList->count(),
        ];
    }

    public function addCondition(AbstractFilterDefinitionType $filterDefinition, ProductListInterface $productList, array $currentFilter, array $params, bool $isPrecondition = false): array
    {
        $value = $params[$filterDefinition->getField()] ?? null;
        $isReload = $params['is_reload'] ?? null;

        if ($value == AbstractFilterType::EMPTY_STRING) {
            $value = null;
        } elseif (empty($value) && !$isReload) {
            $preSelect = false;
            if (method_exists($filterDefinition, 'getPreSelect')) {
                $preSelect = $filterDefinition->getPreSelect();
            }

            $value = $preSelect;
        }

        $currentFilter[$filterDefinition->getField()] = $value;

        $conditions = [];
        if (!empty($value)) {
            $db = Db::get();
            foreach ($value as $category) {
                if (is_object($category)) {
                    $category = $category->getId();
                }

                $category = '%,' . trim((string)$category) . ',%';

                $conditions[] = $filterDefinition->getField() . ' LIKE ' . $db->quote($category);
            }
        }

        if (count($conditions)) {
            $useAndCondition = false;
            if (method_exists($filterDefinition, 'getUseAndCondition')) {
                $useAndCondition = $filterDefinition->getUseAndCondition();
            }

            $conditions = $useAndCondition ? implode(' AND ', $conditions) : '(' . implode(' OR ', $conditions) . ')';

            if ($isPrecondition) {
                $productList->addCondition($conditions, 'PRECONDITION_' . $filterDefinition->getField());
            } else {
                $productList->addCondition($conditions, $filterDefinition->getField());
            }
        }

        return $currentFilter;
    }
}
