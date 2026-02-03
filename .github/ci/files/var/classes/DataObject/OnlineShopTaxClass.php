<?php
declare(strict_types=1);

/**
 * Inheritance: no
 * Variants: no
 *
 * Fields Summary:
 * - taxEntryCombinationType [select]
 * - taxEntries [fieldcollections]
 */

namespace OpenDxp\Model\DataObject;

use OpenDxp\Model\DataObject\Exception\InheritanceParentNotFoundException;
use OpenDxp\Model\DataObject\PreGetValueHookInterface;

/**
* @method static \OpenDxp\Model\DataObject\OnlineShopTaxClass\Listing getList(array $config = [])
* @method static \OpenDxp\Model\DataObject\OnlineShopTaxClass\Listing|\OpenDxp\Model\DataObject\OnlineShopTaxClass|null getByTaxEntryCombinationType(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
*/

class OnlineShopTaxClass extends Concrete
{
protected $classId = "EF_OSTC";
protected $className = "OnlineShopTaxClass";
protected ?string $taxEntryCombinationType = null;
protected ?Fieldcollection $taxEntries = null;


public static function create(array $values = []): static
{
	$object = new static();
	$object->setValues($values);
	return $object;
}

/**
* Get taxEntryCombinationType - Tax Entry Combination Type
* @return string|null
*/
public function getTaxEntryCombinationType(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\OpenDxp::inAdmin()) {
		$preValue = $this->preGetValue("taxEntryCombinationType");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->taxEntryCombinationType;

	if ($data instanceof \OpenDxp\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set taxEntryCombinationType - Tax Entry Combination Type
* @param string|null $taxEntryCombinationType
* @return $this
*/
public function setTaxEntryCombinationType(?string $taxEntryCombinationType): static
{
	$this->taxEntryCombinationType = $taxEntryCombinationType;

	return $this;
}

    public function getTaxEntries(): ?Fieldcollection
{
	if ($this instanceof PreGetValueHookInterface && !\OpenDxp::inAdmin()) {
		$preValue = $this->preGetValue("taxEntries");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("taxEntries")->preGetData($this);
	return $data;
}

/**
* Set taxEntries - Tax Entries
* @param \OpenDxp\Model\DataObject\Fieldcollection|null $taxEntries
* @return $this
*/
public function setTaxEntries(?\OpenDxp\Model\DataObject\Fieldcollection $taxEntries): static
{
	/** @var \OpenDxp\Model\DataObject\ClassDefinition\Data\Fieldcollections $fd */
	$fd = $this->getClass()->getFieldDefinition("taxEntries");
	$this->taxEntries = $fd->preSetData($this, $taxEntries);
	return $this;
}

}

