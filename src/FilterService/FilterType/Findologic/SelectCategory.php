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
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractCategory;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractFilterDefinitionType;
use OpenDxp\Model\DataObject\Fieldcollection\Data\FilterCategory;
use OpenDxp\Model\Element\ElementInterface;

class SelectCategory extends \OpenDxp\Bundle\EcommerceFrameworkBundle\FilterService\FilterType\SelectCategory
{
    const FIELDNAME = 'cat';

    #[\Override]
    public function prepareGroupByValues(AbstractFilterDefinitionType $filterDefinition, ProductListInterface $productList): void
    {
        //$productList->prepareGroupBySystemValues($filterDefinition->getField(), true);
    }

    /**
     * @param FilterCategory $filterDefinition
     *
     * @throws Exception
     */
    #[\Override]
    public function getFilterValues(AbstractFilterDefinitionType $filterDefinition, ProductListInterface $productList, array $currentFilter): array
    {
        $rawValues = $productList->getGroupByValues(self::FIELDNAME, true);
        $values = [];

        foreach ($rawValues as $v) {
            $values[$v['label']] = ['value' => $v['label'], 'count' => $v['count']];
        }

        return [
            'hideFilter' => $filterDefinition->getRequiredFilterField() && empty($currentFilter[$filterDefinition->getRequiredFilterField()]),
            'label' => $filterDefinition->getLabel(),
            'currentValue' => $currentFilter[$filterDefinition->getField()],
            'values' => array_values($values),
            'fieldname' => self::FIELDNAME,
            'rootCategory' => method_exists($filterDefinition, 'getRootCategory') ? $filterDefinition->getRootCategory() : null,
            'resultCount' => $productList->count(),
        ];
    }

    /**
     * @param FilterCategory $filterDefinition
     */
    #[\Override]
    public function addCondition(AbstractFilterDefinitionType $filterDefinition, ProductListInterface $productList, array $currentFilter, array $params, bool $isPrecondition = false): array
    {
        $value = $params[$filterDefinition->getField()] ?? null;
        $isReload = $params['is_reload'] ?? null;

        if ($value == AbstractFilterType::EMPTY_STRING) {
            $value = null;
        } elseif (empty($value) && !$isReload && $filterDefinition instanceof FilterCategory) {
            $value = $filterDefinition->getPreSelect();
            if ($value instanceof ElementInterface) {
                $value = $value->getId();
            }
        }

        $currentFilter[$filterDefinition->getField()] = $value;

        if (!empty($value)) {
            $value = trim($value);
            if ($category = AbstractCategory::getById((int) $value)) {
                $productList->setCategory($category);
            }
        }

        return $currentFilter;
    }
}
