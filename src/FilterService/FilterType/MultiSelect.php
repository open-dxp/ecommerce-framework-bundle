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
use OpenDxp\Model\DataObject\Fieldcollection\Data\FilterMultiSelect;

class MultiSelect extends AbstractFilterType
{
    public function getFilterValues(AbstractFilterDefinitionType $filterDefinition, ProductListInterface $productList, array $currentFilter): array
    {
        $field = $this->getField($filterDefinition);

        if (!$filterDefinition instanceof FilterMultiSelect) {
            throw new InvalidConfigException('invalid configuration');
        }

        $useAndCondition = $filterDefinition->getUseAndCondition();

        return [
            'hideFilter' => $filterDefinition->getRequiredFilterField() && empty($currentFilter[$filterDefinition->getRequiredFilterField()]),
            'label' => $filterDefinition->getLabel(),
            'currentValue' => $currentFilter[$field],
            'values' => $productList->getGroupByValues($field, true, !$useAndCondition),
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

        if (!empty($value) && !is_array($value)) {
            $value = [$value];
        }

        if (empty($value) && !$isReload) {
            if (!empty($preSelect) || $preSelect == '0') {
                $value = explode(',', $preSelect);
            }
        } elseif (!empty($value) && in_array(AbstractFilterType::EMPTY_STRING, $value)) {
            $value = null;
        }

        $currentFilter[$field] = $value;

        if (!empty($value)) {
            $quotedValues = [];
            $db = Db::get();
            foreach ($value as $v) {
                if (!empty($v)) {
                    $quotedValues[] = $db->quote($v);
                }
            }
            if ($quotedValues !== []) {
                if (!$filterDefinition instanceof FilterMultiSelect) {
                    throw new InvalidConfigException('invalid configuration');
                }

                if ($filterDefinition->getUseAndCondition()) {
                    foreach ($quotedValues as $value) {
                        if ($isPrecondition) {
                            $productList->addCondition($field . ' = ' . $value, 'PRECONDITION_' . $field);
                        } else {
                            $productList->addCondition($field . ' = ' . $value, $field);
                        }
                    }
                } elseif ($isPrecondition) {
                    $productList->addCondition($field . ' IN (' . implode(',', $quotedValues) . ')', 'PRECONDITION_' . $field);
                } else {
                    $productList->addCondition($field . ' IN (' . implode(',', $quotedValues) . ')', $field);
                }
            }
        }

        return $currentFilter;
    }
}
