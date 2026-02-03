<?php
declare(strict_types=1);

/**
 * Fields Summary:
 * - localizedfields [localizedfields]
 * -- name [input]
 * - percent [numeric]
 */

namespace OpenDxp\Model\DataObject\Fieldcollection\Data;

use OpenDxp\Model\DataObject;
use OpenDxp\Model\DataObject\PreGetValueHookInterface;

class TaxEntry extends DataObject\Fieldcollection\Data\AbstractData
{
protected string $type = "TaxEntry";
protected ?DataObject\Localizedfield $localizedfields;
protected ?float $percent;


/**
* Get localizedfields -
* @return \OpenDxp\Model\DataObject\Localizedfield|null
*/
public function getLocalizedfields(): ?\OpenDxp\Model\DataObject\Localizedfield
{
	$container = $this;
	/** @var \OpenDxp\Model\DataObject\ClassDefinition\Data\Localizedfields $fd */
	$fd = $this->getDefinition()->getFieldDefinition("localizedfields");
	$data = $fd->preGetData($container);
	if ($data instanceof \OpenDxp\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Get name - Name
 */
public function getName($language = null): ?string
{
	$data = $this->getLocalizedfields()->getLocalizedValue("name", $language);
	if ($data instanceof \OpenDxp\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set localizedfields -
* @param \OpenDxp\Model\DataObject\Localizedfield|null $localizedfields
* @return $this
*/
public function setLocalizedfields(?\OpenDxp\Model\DataObject\Localizedfield $localizedfields): static
{
	$hideUnpublished = \OpenDxp\Model\DataObject\Concrete::getHideUnpublished();
	\OpenDxp\Model\DataObject\Concrete::setHideUnpublished(false);
	$currentData = $this->getLocalizedfields();
	\OpenDxp\Model\DataObject\Concrete::setHideUnpublished($hideUnpublished);
	$this->markFieldDirty("localizedfields", true);
	$this->localizedfields = $localizedfields;

	return $this;
}

/**
* Set name - Name
* @param string|null $name
* @return $this
*/
public function setName (?string $name, $language = null): static
{
	$isEqual = false;
	$this->getLocalizedfields()->setLocalizedValue("name", $name, $language, !$isEqual);

	return $this;
}

/**
* Get percent - Tax Rate in Percent
* @return float|null
*/
public function getPercent(): ?float
{
	$data = $this->percent;
	if ($data instanceof \OpenDxp\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set percent - Tax Rate in Percent
* @param float|null $percent
* @return $this
*/
public function setPercent(?float $percent): static
{
	/** @var \OpenDxp\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getDefinition()->getFieldDefinition("percent");
	$this->percent = $fd->preSetData($this, $percent);
	return $this;
}

}

