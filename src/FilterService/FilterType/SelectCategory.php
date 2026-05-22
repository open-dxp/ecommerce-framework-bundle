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

use Exception;
use OpenDxp;
use OpenDxp\Bundle\EcommerceFrameworkBundle\IndexService\ProductList\ProductListInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractFilterDefinitionType;
use OpenDxp\Db;
use OpenDxp\Model\DataObject\Concrete;
use OpenDxp\Model\Element\ElementInterface;

class SelectCategory extends AbstractFilterType
{
    /**
     * @throws Exception
     */
    public function getFilterValues(AbstractFilterDefinitionType $filterDefinition, ProductListInterface $productList, array $currentFilter): array
    {
        $rawValues = $productList->getGroupByValues($filterDefinition->getField(), true);
        $values = [];

        $availableRelations = [];
        if (method_exists($filterDefinition, 'getAvailableCategories') && $filterDefinition->getAvailableCategories()) {
            /** @var Concrete $rel */
            foreach ($filterDefinition->getAvailableCategories() as $rel) {
                $availableRelations[$rel->getId()] = true;
            }
        }

        foreach ($rawValues as $v) {
            if ($v['value']) {
                $explode = array_map(intval(...), explode(',', (string) $v['value']));
                foreach ($explode as $e) {
                    if ($availableRelations === [] || ($availableRelations[$e] ?? false)) {
                        $count = empty($values[$e]) ? $v['count'] : $values[$e]['count'] + $v['count'];
                        $values[$e] = ['value' => $e, 'count' => $count];
                    }
                }
            }
        }

        $request = OpenDxp::getContainer()->get('request_stack')->getCurrentRequest();

        return [
            'hideFilter' => $filterDefinition->getRequiredFilterField() && empty($currentFilter[$filterDefinition->getRequiredFilterField()]),
            'label' => $filterDefinition->getLabel(),
            'currentValue' => $currentFilter[$filterDefinition->getField()],
            'values' => array_values($values),
            'indexedValues' => $values,
            'fieldname' => $filterDefinition->getField(),
            'metaData' => $filterDefinition->getMetaData(),
            'rootCategory' => method_exists($filterDefinition, 'getRootCategory') ? $filterDefinition->getRootCategory() : null,
            'document' => $request->get('contentDocument'),
            'resultCount' => $productList->count(),
        ];
    }

    public function addCondition(AbstractFilterDefinitionType $filterDefinition, ProductListInterface $productList, array $currentFilter, array $params, bool $isPrecondition = false): array
    {
        $value = $params[$filterDefinition->getField()] ?? null;
        $isReload = $params['is_reload'] ?? null;

        if ($value === AbstractFilterType::EMPTY_STRING) {
            $value = null;
        } elseif (empty($value) && !$isReload && method_exists($filterDefinition, 'getPreSelect')) {
            $value = $filterDefinition->getPreSelect();
            if ($value instanceof ElementInterface) {
                $value = $value->getId();
            }
        }

        $currentFilter[$filterDefinition->getField()] = $value;

        if (!empty($value)) {
            $value = '%,' . trim((string)$value) . ',%';

            $db = Db::get();

            if ($isPrecondition) {
                $productList->addCondition($filterDefinition->getField() . ' LIKE ' . $db->quote($value), 'PRECONDITION_' . $filterDefinition->getField());
            } else {
                $productList->addCondition($filterDefinition->getField() . ' LIKE ' . $db->quote($value), $filterDefinition->getField());
            }
        }

        return $currentFilter;
    }
}
