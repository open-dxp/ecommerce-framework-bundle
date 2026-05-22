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

use OpenDxp\Bundle\EcommerceFrameworkBundle\IndexService\ProductList\ProductListInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\IndexService\Worker\WorkerInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractFilterDefinitionType;
use OpenDxp\Db;

class SelectFromMultiSelect extends AbstractFilterType
{
    public function getFilterValues(AbstractFilterDefinitionType $filterDefinition, ProductListInterface $productList, array $currentFilter): array
    {
        $field = $this->getField($filterDefinition);
        $rawValues = $productList->getGroupByValues($field, true);

        $values = [];
        foreach ($rawValues as $v) {
            $explode = explode(WorkerInterface::MULTISELECT_DELIMITER, $v['value'] ?? '');
            foreach ($explode as $e) {
                if (!empty($e)) {
                    if (!empty($values[$e])) {
                        $values[$e]['count'] += $v['count'];
                    } else {
                        $values[$e] = ['value' => $e, 'count' => $v['count']];
                    }
                }
            }
        }

        return [
            'hideFilter' => $filterDefinition->getRequiredFilterField() && empty($currentFilter[$filterDefinition->getRequiredFilterField()]),
            'label' => $filterDefinition->getLabel(),
            'currentValue' => $currentFilter[$field],
            'values' => array_values($values),
            'fieldname' => $field,
            'metaData' => $filterDefinition->getMetaData(),
            'resultCount' => $productList->count(),
        ];
    }

    public function addCondition(AbstractFilterDefinitionType $filterDefinition, ProductListInterface $productList, array $currentFilter, array $params, bool $isPrecondition = false): array
    {
        $field = $this->getField($filterDefinition);
        $preSelect = $this->getPreSelect($filterDefinition);

        $value = $params[$field] ?? null;
        $isReload = $params['is_reload'] ?? null;

        if ($value == AbstractFilterType::EMPTY_STRING) {
            $value = null;
        } elseif (empty($value) && !$isReload) {
            $value = $preSelect;
        }

        if ($value) {
            $value = trim((string) $value);
        }

        $currentFilter[$field] = $value;
        $db = Db::get();

        if (!empty($value)) {
            $value = '%' . WorkerInterface::MULTISELECT_DELIMITER  . $value .  WorkerInterface::MULTISELECT_DELIMITER . '%';
            if ($isPrecondition) {
                $productList->addCondition($field . ' LIKE ' . $db->quote($value), 'PRECONDITION_' . $field);
            } else {
                $productList->addCondition($field . ' LIKE ' . $db->quote($value), $field);
            }
        }

        return $currentFilter;
    }
}
