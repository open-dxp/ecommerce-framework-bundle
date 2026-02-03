<?php
declare(strict_types=1);

/**
 * Fields Summary:
 * - label [input]
 * - preSelect [manyToManyObjectRelation]
 * - useAndCondition [checkbox]
 * - includeParentCategories [checkbox]
 * - scriptPath [input]
 * - availableCategories [manyToManyObjectRelation]
 */

namespace OpenDxp\Model\DataObject\Fieldcollection\Data;

use OpenDxp\Model\DataObject;
use OpenDxp\Model\DataObject\PreGetValueHookInterface;

class FilterCategoryMultiselect extends \OpenDxp\Bundle\EcommerceFrameworkBundle\Model\CategoryFilterDefinitionType
{
protected string $type = "FilterCategoryMultiselect";
protected ?string $label;
protected array $preSelect;
protected ?bool $useAndCondition;
protected ?bool $includeParentCategories;
protected ?string $scriptPath;
protected array $availableCategories;


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
* Get preSelect - Pre Select
* @return \OpenDxp\Model\DataObject\ProductCategory[]
*/
public function getPreSelect(): array
{
	$container = $this;
	/** @var \OpenDxp\Model\DataObject\ClassDefinition\Data\ManyToManyObjectRelation $fd */
	$fd = $this->getDefinition()->getFieldDefinition("preSelect");
	$data = $fd->preGetData($container);
	if ($data instanceof \OpenDxp\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set preSelect - Pre Select
* @param \OpenDxp\Model\DataObject\ProductCategory[] $preSelect
* @return $this
*/
public function setPreSelect(?array $preSelect): static
{
	/** @var \OpenDxp\Model\DataObject\ClassDefinition\Data\ManyToManyObjectRelation $fd */
	$fd = $this->getDefinition()->getFieldDefinition("preSelect");
	$hideUnpublished = \OpenDxp\Model\DataObject\Concrete::getHideUnpublished();
	\OpenDxp\Model\DataObject\Concrete::setHideUnpublished(false);
	$currentData = $this->getPreSelect();
	\OpenDxp\Model\DataObject\Concrete::setHideUnpublished($hideUnpublished);
	$isEqual = $fd->isEqual($currentData, $preSelect);
	if (!$isEqual) {
		$this->markFieldDirty("preSelect", true);
	}
	$this->preSelect = $fd->preSetData($this, $preSelect);
	return $this;
}

/**
* Get useAndCondition - Use And Condition
* @return bool|null
*/
public function getUseAndCondition(): ?bool
{
	$data = $this->useAndCondition;
	if ($data instanceof \OpenDxp\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set useAndCondition - Use And Condition
* @param bool|null $useAndCondition
* @return $this
*/
public function setUseAndCondition(?bool $useAndCondition): static
{
	$this->useAndCondition = $useAndCondition;

	return $this;
}

/**
* Get includeParentCategories - Include SubCategories
* @return bool|null
*/
public function getIncludeParentCategories(): ?bool
{
	$data = $this->includeParentCategories;
	if ($data instanceof \OpenDxp\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set includeParentCategories - Include SubCategories
* @param bool|null $includeParentCategories
* @return $this
*/
public function setIncludeParentCategories(?bool $includeParentCategories): static
{
	$this->includeParentCategories = $includeParentCategories;

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
* Get availableCategories - Available Categories
* @return \OpenDxp\Model\DataObject\ProductCategory[]
*/
public function getAvailableCategories(): array
{
	$container = $this;
	/** @var \OpenDxp\Model\DataObject\ClassDefinition\Data\ManyToManyObjectRelation $fd */
	$fd = $this->getDefinition()->getFieldDefinition("availableCategories");
	$data = $fd->preGetData($container);
	if ($data instanceof \OpenDxp\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set availableCategories - Available Categories
* @param \OpenDxp\Model\DataObject\ProductCategory[] $availableCategories
* @return $this
*/
public function setAvailableCategories(?array $availableCategories): static
{
	/** @var \OpenDxp\Model\DataObject\ClassDefinition\Data\ManyToManyObjectRelation $fd */
	$fd = $this->getDefinition()->getFieldDefinition("availableCategories");
	$hideUnpublished = \OpenDxp\Model\DataObject\Concrete::getHideUnpublished();
	\OpenDxp\Model\DataObject\Concrete::setHideUnpublished(false);
	$currentData = $this->getAvailableCategories();
	\OpenDxp\Model\DataObject\Concrete::setHideUnpublished($hideUnpublished);
	$isEqual = $fd->isEqual($currentData, $availableCategories);
	if (!$isEqual) {
		$this->markFieldDirty("availableCategories", true);
	}
	$this->availableCategories = $fd->preSetData($this, $availableCategories);
	return $this;
}

}

