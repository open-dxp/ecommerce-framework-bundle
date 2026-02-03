<?php
declare(strict_types=1);

/**
 * Inheritance: no
 * Variants: no
 *
 * Fields Summary:
 * - tokenId [numeric]
 * - token [input]
 * - voucherSeries [manyToOneRelation]
 */

namespace OpenDxp\Model\DataObject;

use OpenDxp\Model\DataObject\Exception\InheritanceParentNotFoundException;
use OpenDxp\Model\DataObject\PreGetValueHookInterface;
use OpenDxp\Model\Element\AbstractElement;

/**
* @method static \OpenDxp\Model\DataObject\OnlineShopVoucherToken\Listing getList(array $config = [])
* @method static \OpenDxp\Model\DataObject\OnlineShopVoucherToken\Listing|\OpenDxp\Model\DataObject\OnlineShopVoucherToken|null getByTokenId(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \OpenDxp\Model\DataObject\OnlineShopVoucherToken\Listing|\OpenDxp\Model\DataObject\OnlineShopVoucherToken|null getByToken(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \OpenDxp\Model\DataObject\OnlineShopVoucherToken\Listing|\OpenDxp\Model\DataObject\OnlineShopVoucherToken|null getByVoucherSeries(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
*/

class OnlineShopVoucherToken extends Concrete
{
protected $classId = "EF_OSVT";
protected $className = "OnlineShopVoucherToken";
protected ?float $tokenId = null;
protected ?string $token = null;
protected \OpenDxp\Model\Element\AbstractElement|null|OnlineShopVoucherSeries $voucherSeries;


public static function create(array $values = []): static
{
	$object = new static();
	$object->setValues($values);
	return $object;
}

/**
* Get tokenId - Token ID
* @return float|null
*/
public function getTokenId(): ?float
{
	if ($this instanceof PreGetValueHookInterface && !\OpenDxp::inAdmin()) {
		$preValue = $this->preGetValue("tokenId");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->tokenId;

	if ($data instanceof \OpenDxp\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set tokenId - Token ID
* @param float|null $tokenId
* @return $this
*/
public function setTokenId(?float $tokenId): static
{
	/** @var \OpenDxp\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getClass()->getFieldDefinition("tokenId");
	$this->tokenId = $fd->preSetData($this, $tokenId);
	return $this;
}

/**
* Get token - Token
* @return string|null
*/
public function getToken(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\OpenDxp::inAdmin()) {
		$preValue = $this->preGetValue("token");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->token;

	if ($data instanceof \OpenDxp\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set token - Token
* @param string|null $token
* @return $this
*/
public function setToken(?string $token): static
{
	$this->token = $token;

	return $this;
}

/**
* Get voucherSeries - Voucher Series
* @return OnlineShopVoucherSeries|\OpenDxp\Model\Element\AbstractElement|\OpenDxp\Model\Element\ElementInterface|null
*/
public function getVoucherSeries(): OnlineShopVoucherSeries|\OpenDxp\Model\Element\ElementInterface|\OpenDxp\Model\Element\AbstractElement|null
{
	if ($this instanceof PreGetValueHookInterface && !\OpenDxp::inAdmin()) {
		$preValue = $this->preGetValue("voucherSeries");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("voucherSeries")->preGetData($this);

	if ($data instanceof \OpenDxp\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set voucherSeries - Voucher Series
* @param \OpenDxp\Model\DataObject\OnlineShopVoucherSeries|null $voucherSeries
* @return $this
*/
public function setVoucherSeries(?\OpenDxp\Model\Element\AbstractElement $voucherSeries): static
{
	/** @var \OpenDxp\Model\DataObject\ClassDefinition\Data\ManyToOneRelation $fd */
	$fd = $this->getClass()->getFieldDefinition("voucherSeries");
	$hideUnpublished = \OpenDxp\Model\DataObject\Concrete::getHideUnpublished();
	\OpenDxp\Model\DataObject\Concrete::setHideUnpublished(false);
	$currentData = $this->getVoucherSeries();
	\OpenDxp\Model\DataObject\Concrete::setHideUnpublished($hideUnpublished);
	$isEqual = $fd->isEqual($currentData, $voucherSeries);
	if (!$isEqual) {
		$this->markFieldDirty("voucherSeries", true);
	}
	$this->voucherSeries = $fd->preSetData($this, $voucherSeries);
	return $this;
}

}

