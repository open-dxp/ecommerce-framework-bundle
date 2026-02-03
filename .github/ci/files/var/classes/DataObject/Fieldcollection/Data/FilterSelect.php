<?php
declare(strict_types=1);

/**
 * Fields Summary:
 * - label [input]
 * - field [indexFieldSelection]
 * - scriptPath [input]
 */

namespace OpenDxp\Model\DataObject\Fieldcollection\Data;

use OpenDxp\Model\DataObject;
use OpenDxp\Model\DataObject\PreGetValueHookInterface;

class FilterSelect extends \OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractFilterDefinitionType
{
protected string $type = "FilterSelect";
protected ?string $label = null;
protected ?\OpenDxp\Bundle\EcommerceFrameworkBundle\CoreExtensions\ObjectData\IndexFieldSelection $field = null;
protected ?string $scriptPath = null;


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

}

