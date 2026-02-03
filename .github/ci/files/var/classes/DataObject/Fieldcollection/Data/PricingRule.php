<?php
declare(strict_types=1);

/**
 * Fields Summary:
 * - ruleId [numeric]
 * - localizedfields [localizedfields]
 * -- name [input]
 */

namespace OpenDxp\Model\DataObject\Fieldcollection\Data;

use OpenDxp\Model\DataObject;
use OpenDxp\Model\DataObject\PreGetValueHookInterface;

class PricingRule extends DataObject\Fieldcollection\Data\AbstractData
{
protected string $type = "PricingRule";
protected ?float $ruleId;
protected ?DataObject\Localizedfield $localizedfields;


/**
* Get ruleId - Rule Id
* @return float|null
*/
public function getRuleId(): ?float
{
	$data = $this->ruleId;
	if ($data instanceof \OpenDxp\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set ruleId - Rule Id
* @param float|null $ruleId
* @return $this
*/
public function setRuleId(?float $ruleId): static
{
	/** @var \OpenDxp\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getDefinition()->getFieldDefinition("ruleId");
	$this->ruleId = $fd->preSetData($this, $ruleId);
	return $this;
}

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

}

