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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\FilterService\FilterType\SearchIndex;

use OpenDxp\Bundle\EcommerceFrameworkBundle\Exception\InvalidConfigException;
use OpenDxp\Bundle\EcommerceFrameworkBundle\FilterService\FilterType\AbstractFilterType;
use OpenDxp\Bundle\EcommerceFrameworkBundle\IndexService\ProductList\ProductListInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractFilterDefinitionType;
use OpenDxp\Model\DataObject\Fieldcollection\Data\FilterMultiSelectFromMultiSelect;
use Override;

class MultiSelectFromMultiSelect extends \OpenDxp\Bundle\EcommerceFrameworkBundle\FilterService\FilterType\MultiSelectFromMultiSelect
{
    #[Override]
    public function prepareGroupByValues(AbstractFilterDefinitionType $filterDefinition, ProductListInterface $productList): void
    {
        $useAndCondition = false;

        if (!$filterDefinition instanceof FilterMultiSelectFromMultiSelect) {
            throw new InvalidConfigException('invalid configuration');
        }

        $useAndCondition = $filterDefinition->getUseAndCondition();

        $field = $this->getField($filterDefinition);
        $productList->prepareGroupByValues($field, true, !$useAndCondition);
    }

    /**
     * @return string[]
     */
    #[Override]
    public function addCondition(AbstractFilterDefinitionType $filterDefinition, ProductListInterface $productList, array $currentFilter, array $params, bool $isPrecondition = false): array
    {
        $field = $this->getField($filterDefinition);
        $preSelect = $this->getPreSelect($filterDefinition);

        $value = $params[$field] ?? null;
        $isReload = $params['is_reload'] ?? null;

        if (empty($value) && !$isReload) {
            $value = is_array($preSelect) ? $preSelect : explode(',', (string) $preSelect);

            foreach ($value as $key => $v) {
                if (!$v) {
                    unset($value[$key]);
                }
            }
        } elseif (!empty($value) && in_array(AbstractFilterType::EMPTY_STRING, $value)) {
            $value = null;
        }

        $currentFilter[$field] = $value;

        if (!empty($value)) {
            if (!$filterDefinition instanceof FilterMultiSelectFromMultiSelect) {
                throw new InvalidConfigException('invalid configuration');
            }

            if ($filterDefinition->getUseAndCondition()) {
                foreach ($value as $entry) {
                    $productList->addCondition(['term' => ['attributes.' . $field => $entry]], $field);
                }
            } else {
                $boolArray = [];
                foreach ($value as $entry) {
                    $boolArray[] = ['term' => ['attributes.' . $field => $entry]];
                }

                $productList->addCondition(['bool' => ['should' => $boolArray, 'minimum_should_match' => 1]], $field);
            }
        }

        return $currentFilter;
    }
}
