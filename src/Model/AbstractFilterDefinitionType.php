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

use OpenDxp\Bundle\EcommerceFrameworkBundle\CoreExtensions\ObjectData\IndexFieldSelection;

/**
 * Abstract base class for filter definition type field collections
 */
abstract class AbstractFilterDefinitionType extends \OpenDxp\Model\DataObject\Fieldcollection\Data\AbstractData
{
    protected array $metaData = [];

    public function getMetaData(): array
    {
        return $this->metaData;
    }

    public function setMetaData(array $metaData): static
    {
        $this->metaData = $metaData;

        return $this;
    }

    abstract public function getLabel(): ?string;

    abstract public function getField(): string|IndexFieldSelection|null;

    abstract public function getScriptPath(): ?string;

    public function getRequiredFilterField(): string
    {
        return '';
    }
}
