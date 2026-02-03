<?php
declare(strict_types=1);

/**
 * Inheritance: no
 * Variants: no
 *
 * Fields Summary:
 * - name [input]
 * - tokenSettings [fieldcollections]
 */

namespace OpenDxp\Model\DataObject;

use OpenDxp\Model\DataObject\Exception\InheritanceParentNotFoundException;
use OpenDxp\Model\DataObject\PreGetValueHookInterface;

/**
* @method static \OpenDxp\Model\DataObject\OnlineShopVoucherSeries\Listing getList(array $config = [])
* @method static \OpenDxp\Model\DataObject\OnlineShopVoucherSeries\Listing|\OpenDxp\Model\DataObject\OnlineShopVoucherSeries|null getByName(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
*/

class OnlineShopVoucherSeries extends \OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractVoucherSeries
{
protected $classId = "EF_OSVS";
protected $className = "OnlineShopVoucherSeries";
protected ?string $name = null;
protected ?Fieldcollection $tokenSettings = null;


public static function create(array $values = []): static
{
	$object = new static();
	$object->setValues($values);
	return $object;
}

/**
* Get name - Name
* @return string|null
*/
public function getName(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\OpenDxp::inAdmin()) {
		$preValue = $this->preGetValue("name");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->name;

	if ($data instanceof \OpenDxp\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set name - Name
* @param string|null $name
* @return $this
*/
public function setName(?string $name): static
{
	$this->name = $name;

	return $this;
}

    public function getTokenSettings(): ?Fieldcollection
{
	if ($this instanceof PreGetValueHookInterface && !\OpenDxp::inAdmin()) {
		$preValue = $this->preGetValue("tokenSettings");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("tokenSettings")->preGetData($this);
	return $data;
}

/**
* Set tokenSettings - Token Settings
* @param \OpenDxp\Model\DataObject\Fieldcollection|null $tokenSettings
* @return $this
*/
public function setTokenSettings(?\OpenDxp\Model\DataObject\Fieldcollection $tokenSettings): static
{
	/** @var \OpenDxp\Model\DataObject\ClassDefinition\Data\Fieldcollections $fd */
	$fd = $this->getClass()->getFieldDefinition("tokenSettings");
	$this->tokenSettings = $fd->preSetData($this, $tokenSettings);
	return $this;
}

}

