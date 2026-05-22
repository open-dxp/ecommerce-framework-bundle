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
use OpenDxp\Bundle\EcommerceFrameworkBundle\IndexService\ProductList\ProductListInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractFilterDefinitionType;
use OpenDxp\Db;
use OpenDxp\Logger;
use OpenDxp\Model\DataObject;
use OpenDxp\Model\DataObject\Fieldcollection\Data\FilterMultiRelation;
use OpenDxp\Model\DataObject\Folder;

class MultiSelectRelation extends AbstractFilterType
{
    /**
     * @param FilterMultiRelation $filterDefinition
     *
     * @throws Exception
     */
    public function getFilterValues(AbstractFilterDefinitionType $filterDefinition, ProductListInterface $productList, array $currentFilter): array
    {
        $field = $this->getField($filterDefinition);
        $values = $productList->getGroupByRelationValues($field, true, !$filterDefinition->getUseAndCondition());

        $objects = [];
        Logger::info('Load Objects...');
        $availableRelations = [];
        if ($filterDefinition->getAvailableRelations()) {
            $availableRelations = $this->loadAllAvailableRelations($filterDefinition->getAvailableRelations());
        }

        foreach ($values as $v) {
            if ($availableRelations === [] || ($availableRelations[$v['value']] ?? false) === true) {
                $objects[$v['value']] = DataObject::getById($v['value']);
            }
        }
        Logger::info('done.');

        return [
            'hideFilter' => $filterDefinition->getRequiredFilterField() && empty($currentFilter[$filterDefinition->getRequiredFilterField()]),
            'label' => $filterDefinition->getLabel(),
            'currentValue' => $currentFilter[$field],
            'values' => $values,
            'objects' => $objects,
            'fieldname' => $field,
            'metaData' => $filterDefinition->getMetaData(),
            'resultCount' => $productList->count(),
        ];
    }

    /**
     * @param DataObject\AbstractObject[] $availableRelations
     * @param array<int, true> $availableRelationsArray
     *
     * @return array<int, true>
     */
    protected function loadAllAvailableRelations(array $availableRelations, array $availableRelationsArray = []): array
    {
        foreach ($availableRelations as $rel) {
            if ($rel instanceof Folder) {
                $availableRelationsArray = $this->loadAllAvailableRelations($rel->getChildren()->load(), $availableRelationsArray);
            } else {
                $availableRelationsArray[$rel->getId()] = true;
            }
        }

        return $availableRelationsArray;
    }

    /**
     * @param FilterMultiRelation $filterDefinition
     */
    public function addCondition(AbstractFilterDefinitionType $filterDefinition, ProductListInterface $productList, array $currentFilter, array $params, bool $isPrecondition = false): array
    {
        $field = $this->getField($filterDefinition);
        $preSelect = $this->getPreSelect($filterDefinition);

        $value = $params[$field] ?? null;
        $isReload = $params['is_reload'] ?? null;

        if (empty($value) && !$isReload) {
            $objects = $preSelect ?: [];
            $value = [];

            if (!is_array($objects)) {
                $objects = explode(',', (string) $objects);
            }

            foreach ($objects as $o) {
                $value[] = is_object($o) ? $o->getId() : $o;
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
                if ($filterDefinition->getUseAndCondition()) {
                    foreach ($quotedValues as $value) {
                        $productList->addRelationCondition($field, 'dest = ' . $value);
                    }
                } else {
                    $productList->addRelationCondition($field, 'dest IN (' . implode(',', $quotedValues) . ')');
                }
            }
        }

        return $currentFilter;
    }
}
