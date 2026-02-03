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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\FilterService\FilterType\Findologic;

use Exception;
use OpenDxp\Bundle\EcommerceFrameworkBundle\FilterService\FilterType\AbstractFilterType;
use OpenDxp\Bundle\EcommerceFrameworkBundle\IndexService\ProductList\ProductListInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractFilterDefinitionType;
use OpenDxp\Model\DataObject\Fieldcollection\Data\FilterNumberRangeSelection;
use Override;

class NumberRangeSelection extends \OpenDxp\Bundle\EcommerceFrameworkBundle\FilterService\FilterType\NumberRangeSelection
{
    #[Override]
    public function prepareGroupByValues(AbstractFilterDefinitionType $filterDefinition, ProductListInterface $productList): void
    {
        //$productList->prepareGroupByValues($this->getField($filterDefinition), true);
    }

    /**
     * @param FilterNumberRangeSelection $filterDefinition
     *
     * @throws Exception
     */
    #[Override]
    public function getFilterValues(AbstractFilterDefinitionType $filterDefinition, ProductListInterface $productList, array $currentFilter): array
    {
        $field = $this->getField($filterDefinition);
        $ranges = $filterDefinition->getRanges();

        $groupByValues = $productList->getGroupByValues($field, true);

        $counts = [];
        foreach ($ranges->getData() as $row) {
            $counts[$row['from'] . '_' . $row['to']] = 0;
        }

        foreach ($groupByValues as $groupByValue) {
            if ($groupByValue['label']) {
                $value = (float)$groupByValue['label'];

                if (!$value) {
                    $value = 0;
                }
                foreach ($ranges->getData() as $row) {
                    if ((empty($row['from']) || ($row['from'] <= $value)) && (empty($row['to']) || $row['to'] >= $value)) {
                        $counts[$row['from'] . '_' . $row['to']] += $groupByValue['count'];

                        break;
                    }
                }
            }
        }
        $values = [];
        foreach ($ranges->getData() as $row) {
            if ($counts[$row['from'] . '_' . $row['to']]) {
                $values[] = ['from' => $row['from'], 'to' => $row['to'], 'label' => $this->createLabel($row), 'count' => $counts[$row['from'] . '_' . $row['to']], 'unit' => $filterDefinition->getUnit()];
            }
        }

        $currentValue = '';
        if ($currentFilter[$field]['from'] || $currentFilter[$field]['to']) {
            $currentValue = implode('-', $currentFilter[$field]);
        }

        return [
            'hideFilter' => $filterDefinition->getRequiredFilterField() && empty($currentFilter[$filterDefinition->getRequiredFilterField()]),
            'label' => $filterDefinition->getLabel(),
            'currentValue' => $currentValue,
            'currentNiceValue' => $this->createLabel($currentFilter[$field]),
            'unit' => $filterDefinition->getUnit(),
            'values' => $values,
            'definition' => $filterDefinition,
            'fieldname' => $field,
            'resultCount' => $productList->count(),
        ];
    }

    /**
     * @param FilterNumberRangeSelection $filterDefinition
     */
    #[Override]
    public function addCondition(AbstractFilterDefinitionType $filterDefinition, ProductListInterface $productList, array $currentFilter, array $params, bool $isPrecondition = false): array
    {
        $field = $this->getField($filterDefinition);
        $rawValue = $params[$field] ?? null;

        if (!empty($rawValue) && $rawValue != AbstractFilterType::EMPTY_STRING) {
            $values = explode('-', $rawValue);
            $value['from'] = trim($values[0]);
            $value['to'] = trim($values[1]);
        } elseif ($rawValue == AbstractFilterType::EMPTY_STRING) {
            $value = null;
        } else {
            $value = ['from' => null, 'to' => null];
            if (method_exists($filterDefinition, 'getPreSelectFrom')) {
                $value['from'] = $filterDefinition->getPreSelectFrom();
            }
            if (method_exists($filterDefinition, 'getPreSelectTo')) {
                $value['to'] = $filterDefinition->getPreSelectTo();
            }
        }

        $currentFilter[$field] = $value;

        if ($value['from'] || $value['to']) {
            $v = [];
            $v['min'] = $value['from'] ?: 0;

            if ($value['to']) {
                $v['max'] = $value['to'];
            } else {
                $v['max'] = 9999999999999999;       // findologic won't accept only one of max or min, always needs both
            }
            $productList->addCondition($v, $field);
        }

        return $currentFilter;
    }

    private function createLabel(mixed $data): string
    {
        if (is_array($data)) {
            if (!empty($data['from'])) {
                if (!empty($data['to'])) {
                    return $data['from'] . ' - ' . $data['to'];
                }

                return $this->translator->trans('more than') . ' ' . $data['from'];
            }
            if (!empty($data['to'])) {
                return $this->translator->trans('less than') . ' ' . $data['to'];
            }
        }

        return '';
    }
}
