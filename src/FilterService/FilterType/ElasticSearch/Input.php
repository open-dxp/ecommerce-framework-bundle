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
use OpenDxp\Bundle\EcommerceFrameworkBundle\IndexService\ProductList\ProductListInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractFilterDefinitionType;
use OpenDxp\Model\DataObject\Fieldcollection\Data\FilterInputfield;

/**
 * @deprecated This class will be moved to the SearchIndex namespace in version 2.0.0.
 */
class Input extends \OpenDxp\Bundle\EcommerceFrameworkBundle\FilterService\FilterType\Input
{
    public function addCondition(AbstractFilterDefinitionType $filterDefinition, ProductListInterface $productList, array $currentFilter, array $params, bool $isPrecondition = false): array
    {
        $field = $this->getField($filterDefinition);

        if (!$filterDefinition instanceof FilterInputfield) {
            throw new InvalidConfigException('invalid config');
        }
        $preSelect = $filterDefinition->getPreSelect();

        $value = $params[$field] ?? null;
        $isReload = $params['is_reload'] ?? null;

        if ($value == AbstractFilterType::EMPTY_STRING) {
            $value = null;
        } elseif (empty($value) && !$isReload) {
            $value = $preSelect;
        }

        if (is_string($value)) {
            $value = trim($value);
        }

        $currentFilter[$field] = $value;

        if (!empty($value)) {
            $value = '.*"' . $value .  '".*';
            $productList->addCondition(['regexp' => ['attributes.' . $field => $value]], $field);
        }

        return $currentFilter;
    }
}
