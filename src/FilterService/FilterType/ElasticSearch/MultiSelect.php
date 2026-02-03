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

use OpenDxp\Bundle\EcommerceFrameworkBundle\Exception\InvalidConfigException;
use OpenDxp\Bundle\EcommerceFrameworkBundle\FilterService\FilterType\AbstractFilterType;
use OpenDxp\Bundle\EcommerceFrameworkBundle\IndexService\ProductList\ElasticSearch\AbstractElasticSearch;
use OpenDxp\Bundle\EcommerceFrameworkBundle\IndexService\ProductList\ProductListInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractFilterDefinitionType;
use OpenDxp\Model\DataObject\Fieldcollection\Data\FilterMultiSelect;

/**
 * @deprecated This class will be moved to the SearchIndex namespace in version 2.0.0.
 */
class MultiSelect extends \OpenDxp\Bundle\EcommerceFrameworkBundle\FilterService\FilterType\MultiSelect
{
    #[\Override]
    public function prepareGroupByValues(AbstractFilterDefinitionType $filterDefinition, ProductListInterface $productList): void
    {
        if (!$filterDefinition instanceof FilterMultiSelect) {
            throw new InvalidConfigException('invalid configuration');
        }

        $field = $this->getField($filterDefinition);
        $productList->prepareGroupByValues($field, true, !$filterDefinition->getUseAndCondition());
    }

    /**
     * @param FilterMultiSelect $filterDefinition
     */
    #[\Override]
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
            foreach ($value as $v) {
                if (!empty($v)) {
                    $quotedValues[] = $v;
                }
            }

            if (!$productList instanceof AbstractElasticSearch) {
                throw new InvalidConfigException('invalid configuration');
            }

            $tenantConfig = $productList->getTenantConfig();
            $attributeConfig = $tenantConfig->getAttributeConfig()[$field];
            if ($attributeConfig['type'] == 'boolean') {
                foreach ($quotedValues as $k => $v) {
                    $quotedValues[$k] = (bool)$v;
                }
            }

            if ($quotedValues !== []) {
                if ($filterDefinition->getUseAndCondition()) {
                    foreach ($quotedValues as $value) {
                        $productList->addCondition($value, $field);
                    }
                } else {
                    $productList->addCondition(['terms' => ['attributes.' . $field => $quotedValues]], $field);
                }
            }
        }

        return $currentFilter;
    }
}
