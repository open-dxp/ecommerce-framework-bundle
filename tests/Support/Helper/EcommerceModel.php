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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\Tests\Support\Helper;

use OpenDxp\Model\DataObject\ClassDefinition;
use OpenDxp\Model\DataObject\ClassDefinition\Data;
use OpenDxp\Tests\Support\Helper\Model;

class EcommerceModel extends Model
{
    protected function createClass(string $name, ClassDefinition\Layout $layout, string $filename, bool $inheritanceAllowed = false, ?string $id = null): ClassDefinition
    {
        //add index selection fields
        $rootPanel = $layout->getChildren();
        $mainPanel = $rootPanel[0]->getChildren()[0];

        $mainPanel->addChild($this->createDataChild('indexFieldSelection', 'indexFieldSelection', false));
        $mainPanel->addChild($this->createDataChild('indexFieldSelectionCombo', 'indexFieldSelectionCombo', false));
        $mainPanel->addChild($this->createDataChild('indexFieldSelectionField', 'indexFieldSelectionField', false));

        return parent::createClass($name, $layout, $filename, $inheritanceAllowed, $id);
    }

    public function createDataChild(string $type, ?string $name = null, bool $mandatory = false, bool $index = false, bool $visibleInGridView = true, bool $visibleInSearchResult = true): Data
    {
        if (!$name) {
            $name = $type;
        }

        if (strpos($type, 'indexField') === 0) {
            $classname = 'OpenDxp\\Bundle\\EcommerceFrameworkBundle\\CoreExtensions\\ClassDefinition\\' . ucfirst($type);
        } else {
            $classname = 'OpenDxp\\Model\\DataObject\\ClassDefinition\Data\\' . ucfirst($type);
        }
        /** @var Data $child */
        $child = new $classname();
        $child->setName($name);
        $child->setTitle($name);
        $child->setMandatory($mandatory);
        $child->setIndex($index);
        $child->setVisibleGridView($visibleInGridView);
        $child->setVisibleSearch($visibleInSearchResult);

        return $child;
    }
}
