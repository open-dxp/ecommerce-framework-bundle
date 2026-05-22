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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\Model;

use OpenDxp\Model\DataObject;
use OpenDxp\Model\DataObject\Exception\InheritanceParentNotFoundException;
use OpenDxp\Model\DataObject\Fieldcollection;

/**
 * Abstract base class for filter definition pimcore objects
 */
abstract class AbstractFilterDefinition extends DataObject\Concrete implements DataObject\PreGetValueHookInterface
{
    /**
     * returns page limit for product list
     */
    abstract public function getPageLimit(): ?float;

    /**
     * returns list of available fields for sorting ascending
     */
    abstract public function getOrderByAsc(): ?string;

    /**
     * returns list of available fields for sorting descending
     */
    abstract public function getOrderByDesc(): ?string;

    /**
     * return array of field collections for preconditions
     *
     *
     * @return Fieldcollection<AbstractFilterDefinitionType>|null
     */
    abstract public function getConditions(): ?Fieldcollection;

    /**
     * return array of field collections for filters
     *
     *
     * @return Fieldcollection<AbstractFilterDefinitionType>|null
     */
    abstract public function getFilters(): ?Fieldcollection;

    /**
     * enables inheritance for field collections, if xxxInheritance field is available and set to string 'true'
     */
    public function preGetValue(string $key): ?Fieldcollection
    {
        $fd = $this->getClass()->getFieldDefinition($key);

        if (
            $this->getClass()->getAllowInherit() &&
            DataObject::doGetInheritedValues() &&
            $fd instanceof DataObject\ClassDefinition\Data\Fieldcollections
        ) {
            $checkInheritanceKey = $key . 'Inheritance';
            if ($this->{
                'get' . $checkInheritanceKey
                }() == 'true'
            ) {
                try {
                    $parentValue = $this->getValueFromParent($key);
                } catch (InheritanceParentNotFoundException) {
                    $parentValue = null;
                }

                $data = $this->$key;
                if (!$data) {
                    $data = $fd->preGetData($this);
                }
                if (!$data) {
                    return $parentValue;
                }
                if (!empty($parentValue)) {
                    $value = new Fieldcollection($parentValue->getItems());
                    foreach ($data as $entry) {
                        $value->add($entry);
                    }
                } else {
                    $value = new Fieldcollection($data->getItems());
                }

                return $value;
            }
        }

        return null;
    }
}
