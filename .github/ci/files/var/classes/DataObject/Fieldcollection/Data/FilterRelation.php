<?php
declare(strict_types=1);

/**
 * Fields Summary:
 * - label [input]
 * - field [indexFieldSelection]
 * - scriptPath [input]
 * - availableRelations [manyToManyRelation]
 */

namespace OpenDxp\Model\DataObject\Fieldcollection\Data;

use OpenDxp\Model\DataObject;
use OpenDxp\Model\DataObject\PreGetValueHookInterface;

class FilterRelation extends \OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractFilterDefinitionType
{
protected string $type = "FilterRelation";
protected ?string $label = null;
protected ?\OpenDxp\Bundle\EcommerceFrameworkBundle\CoreExtensions\ObjectData\IndexFieldSelection $field = null;
protected ?string $scriptPath = null;
protected array $availableRelations;


/**
* Get label - Label
* @return string|null
*/
public function getLabel(): ?string
{
	$data = $this->label;
	if ($data instanceof \OpenDxp\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set label - Label
* @param string|null $label
* @return $this
*/
public function setLabel(?string $label): static
{
	$this->label = $label;

	return $this;
}

/**
* Get field - Field
* @return \OpenDxp\Bundle\EcommerceFrameworkBundle\CoreExtensions\ObjectData\IndexFieldSelection|null
*/
public function getField(): ?\OpenDxp\Bundle\EcommerceFrameworkBundle\CoreExtensions\ObjectData\IndexFieldSelection
{
	$data = $this->field;
	if ($data instanceof \OpenDxp\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set field - Field
* @param \OpenDxp\Bundle\EcommerceFrameworkBundle\CoreExtensions\ObjectData\IndexFieldSelection|null $field
* @return $this
*/
public function setField(?\OpenDxp\Bundle\EcommerceFrameworkBundle\CoreExtensions\ObjectData\IndexFieldSelection $field): static
{
	$this->field = $field;

	return $this;
}

/**
* Get scriptPath - Script Path
* @return string|null
*/
public function getScriptPath(): ?string
{
	$data = $this->scriptPath;
	if ($data instanceof \OpenDxp\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set scriptPath - Script Path
* @param string|null $scriptPath
* @return $this
*/
public function setScriptPath(?string $scriptPath): static
{
	$this->scriptPath = $scriptPath;

	return $this;
}

/**
* Get availableRelations - Available Relations
* @return \OpenDxp\Model\DataObject\AbstractObject[]
*/
public function getAvailableRelations(): array
{
	$container = $this;
	/** @var \OpenDxp\Model\DataObject\ClassDefinition\Data\ManyToManyRelation $fd */
	$fd = $this->getDefinition()->getFieldDefinition("availableRelations");
	$data = $fd->preGetData($container);
	if ($data instanceof \OpenDxp\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set availableRelations - Available Relations
* @param \OpenDxp\Model\DataObject\AbstractObject[] $availableRelations
* @return $this
*/
public function setAvailableRelations(?array $availableRelations): static
{
	/** @var \OpenDxp\Model\DataObject\ClassDefinition\Data\ManyToManyRelation $fd */
	$fd = $this->getDefinition()->getFieldDefinition("availableRelations");
	$hideUnpublished = \OpenDxp\Model\DataObject\Concrete::getHideUnpublished();
	\OpenDxp\Model\DataObject\Concrete::setHideUnpublished(false);
	$currentData = $this->getAvailableRelations();
	\OpenDxp\Model\DataObject\Concrete::setHideUnpublished($hideUnpublished);
	$isEqual = $fd->isEqual($currentData, $availableRelations);
	if (!$isEqual) {
		$this->markFieldDirty("availableRelations", true);
	}
	$this->availableRelations = $fd->preSetData($this, $availableRelations);
	return $this;
}

}

