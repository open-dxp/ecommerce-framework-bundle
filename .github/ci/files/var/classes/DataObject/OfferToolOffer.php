<?php

/**
 * Inheritance: no
 * Variants: no
 *
 * Fields Summary:
 * - offernumber [input]
 * - dateCreated [datetime]
 * - dateValidUntil [date]
 * - totalPriceBeforeDiscount [numeric]
 * - totalPrice [numeric]
 * - discountType [select]
 * - discount [numeric]
 * - cartId [input]
 * - items [manyToManyObjectRelation]
 * - customItems [manyToManyObjectRelation]
 */

namespace OpenDxp\Model\DataObject;

use OpenDxp\Model\DataObject\Exception\InheritanceParentNotFoundException;
use OpenDxp\Model\DataObject\PreGetValueHookInterface;

/**
* @method static \OpenDxp\Model\DataObject\OfferToolOffer\Listing getList(array $config = [])
* @method static \OpenDxp\Model\DataObject\OfferToolOffer\Listing|\OpenDxp\Model\DataObject\OfferToolOffer|null getByOffernumber(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \OpenDxp\Model\DataObject\OfferToolOffer\Listing|\OpenDxp\Model\DataObject\OfferToolOffer|null getByDateCreated(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \OpenDxp\Model\DataObject\OfferToolOffer\Listing|\OpenDxp\Model\DataObject\OfferToolOffer|null getByDateValidUntil(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \OpenDxp\Model\DataObject\OfferToolOffer\Listing|\OpenDxp\Model\DataObject\OfferToolOffer|null getByTotalPriceBeforeDiscount(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \OpenDxp\Model\DataObject\OfferToolOffer\Listing|\OpenDxp\Model\DataObject\OfferToolOffer|null getByTotalPrice(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \OpenDxp\Model\DataObject\OfferToolOffer\Listing|\OpenDxp\Model\DataObject\OfferToolOffer|null getByDiscountType(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \OpenDxp\Model\DataObject\OfferToolOffer\Listing|\OpenDxp\Model\DataObject\OfferToolOffer|null getByDiscount(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \OpenDxp\Model\DataObject\OfferToolOffer\Listing|\OpenDxp\Model\DataObject\OfferToolOffer|null getByCartId(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \OpenDxp\Model\DataObject\OfferToolOffer\Listing|\OpenDxp\Model\DataObject\OfferToolOffer|null getByItems(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \OpenDxp\Model\DataObject\OfferToolOffer\Listing|\OpenDxp\Model\DataObject\OfferToolOffer|null getByCustomItems(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
*/

class OfferToolOffer extends \OpenDxp\Bundle\EcommerceFrameworkBundle\OfferTool\AbstractOffer
{
public const FIELD_OFFERNUMBER = 'offernumber';
public const FIELD_DATE_CREATED = 'dateCreated';
public const FIELD_DATE_VALID_UNTIL = 'dateValidUntil';
public const FIELD_TOTAL_PRICE_BEFORE_DISCOUNT = 'totalPriceBeforeDiscount';
public const FIELD_TOTAL_PRICE = 'totalPrice';
public const FIELD_DISCOUNT_TYPE = 'discountType';
public const FIELD_DISCOUNT = 'discount';
public const FIELD_CART_ID = 'cartId';
public const FIELD_ITEMS = 'items';
public const FIELD_CUSTOM_ITEMS = 'customItems';

protected $classId = "EF_OTO";
protected $className = "OfferToolOffer";
protected $offernumber;
protected $dateCreated;
protected $dateValidUntil;
protected $totalPriceBeforeDiscount;
protected $totalPrice;
protected $discountType;
protected $discount;
protected $cartId;
protected $items;
protected $customItems;


/**
* @param array $values
* @return static
*/
public static function create(array $values = []): static
{
	$object = new static();
	$object->setValues($values);
	return $object;
}

/**
* Get offernumber - Offernumber
* @return string|null
*/
public function getOffernumber(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\OpenDxp::inAdmin()) {
		$preValue = $this->preGetValue("offernumber");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->offernumber;

	if ($data instanceof \OpenDxp\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set offernumber - Offernumber
* @param string|null $offernumber
* @return $this
*/
public function setOffernumber(?string $offernumber): static
{
	$this->markFieldDirty("offernumber", true);

	$this->offernumber = $offernumber;

	return $this;
}

/**
* Get dateCreated - CreationDate
* @return \Carbon\Carbon|null
*/
public function getDateCreated(): ?\Carbon\Carbon
{
	if ($this instanceof PreGetValueHookInterface && !\OpenDxp::inAdmin()) {
		$preValue = $this->preGetValue("dateCreated");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->dateCreated;

	if ($data instanceof \OpenDxp\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set dateCreated - CreationDate
* @param \Carbon\Carbon|null $dateCreated
* @return $this
*/
public function setDateCreated(?\Carbon\Carbon $dateCreated): static
{
	$this->markFieldDirty("dateCreated", true);

	$this->dateCreated = $dateCreated;

	return $this;
}

/**
* Get dateValidUntil - Valid Until
* @return \Carbon\Carbon|null
*/
public function getDateValidUntil(): ?\Carbon\Carbon
{
	if ($this instanceof PreGetValueHookInterface && !\OpenDxp::inAdmin()) {
		$preValue = $this->preGetValue("dateValidUntil");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->dateValidUntil;

	if ($data instanceof \OpenDxp\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set dateValidUntil - Valid Until
* @param \Carbon\Carbon|null $dateValidUntil
* @return $this
*/
public function setDateValidUntil(?\Carbon\Carbon $dateValidUntil): static
{
	$this->markFieldDirty("dateValidUntil", true);

	$this->dateValidUntil = $dateValidUntil;

	return $this;
}

/**
* Get totalPriceBeforeDiscount - Total Price Before Discount
* @return string|null
*/
public function getTotalPriceBeforeDiscount(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\OpenDxp::inAdmin()) {
		$preValue = $this->preGetValue("totalPriceBeforeDiscount");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->totalPriceBeforeDiscount;

	if ($data instanceof \OpenDxp\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set totalPriceBeforeDiscount - Total Price Before Discount
* @param string|null $totalPriceBeforeDiscount
* @return $this
*/
public function setTotalPriceBeforeDiscount(?string $totalPriceBeforeDiscount): static
{
	/** @var \OpenDxp\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getClass()->getFieldDefinition("totalPriceBeforeDiscount");
	$this->totalPriceBeforeDiscount = $fd->preSetData($this, $totalPriceBeforeDiscount);
	return $this;
}

/**
* Get totalPrice - TotalPrice
* @return string|null
*/
public function getTotalPrice(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\OpenDxp::inAdmin()) {
		$preValue = $this->preGetValue("totalPrice");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->totalPrice;

	if ($data instanceof \OpenDxp\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set totalPrice - TotalPrice
* @param string|null $totalPrice
* @return $this
*/
public function setTotalPrice(?string $totalPrice): static
{
	/** @var \OpenDxp\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getClass()->getFieldDefinition("totalPrice");
	$this->totalPrice = $fd->preSetData($this, $totalPrice);
	return $this;
}

/**
* Get discountType - Discount Type
* @return string|null
*/
public function getDiscountType(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\OpenDxp::inAdmin()) {
		$preValue = $this->preGetValue("discountType");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->discountType;

	if ($data instanceof \OpenDxp\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set discountType - Discount Type
* @param string|null $discountType
* @return $this
*/
public function setDiscountType(?string $discountType): static
{
	$this->markFieldDirty("discountType", true);

	$this->discountType = $discountType;

	return $this;
}

/**
* Get discount - Discount
* @return string|null
*/
public function getDiscount(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\OpenDxp::inAdmin()) {
		$preValue = $this->preGetValue("discount");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->discount;

	if ($data instanceof \OpenDxp\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set discount - Discount
* @param string|null $discount
* @return $this
*/
public function setDiscount(?string $discount): static
{
	/** @var \OpenDxp\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getClass()->getFieldDefinition("discount");
	$this->discount = $fd->preSetData($this, $discount);
	return $this;
}

/**
* Get cartId - Cart Id
* @return string|null
*/
public function getCartId(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\OpenDxp::inAdmin()) {
		$preValue = $this->preGetValue("cartId");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->cartId;

	if ($data instanceof \OpenDxp\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set cartId - Cart Id
* @param string|null $cartId
* @return $this
*/
public function setCartId(?string $cartId): static
{
	$this->markFieldDirty("cartId", true);

	$this->cartId = $cartId;

	return $this;
}

/**
* Get items - Items
* @return \OpenDxp\Model\DataObject\OfferToolOfferItem[]
*/
public function getItems(): array
{
	if ($this instanceof PreGetValueHookInterface && !\OpenDxp::inAdmin()) {
		$preValue = $this->preGetValue("items");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("items")->preGetData($this);

	if ($data instanceof \OpenDxp\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set items - Items
* @param \OpenDxp\Model\DataObject\OfferToolOfferItem[] $items
* @return $this
*/
public function setItems(?array $items): static
{
	/** @var \OpenDxp\Model\DataObject\ClassDefinition\Data\ManyToManyObjectRelation $fd */
	$fd = $this->getClass()->getFieldDefinition("items");
	$hideUnpublished = \OpenDxp\Model\DataObject\Concrete::getHideUnpublished();
	\OpenDxp\Model\DataObject\Concrete::setHideUnpublished(false);
	$currentData = $this->getItems();
	\OpenDxp\Model\DataObject\Concrete::setHideUnpublished($hideUnpublished);
	$isEqual = $fd->isEqual($currentData, $items);
	if (!$isEqual) {
		$this->markFieldDirty("items", true);
	}
	$this->items = $fd->preSetData($this, $items);
	return $this;
}

/**
* Get customItems - Custom Items
* @return \OpenDxp\Model\DataObject\OfferToolOfferItem[]
*/
public function getCustomItems(): array
{
	if ($this instanceof PreGetValueHookInterface && !\OpenDxp::inAdmin()) {
		$preValue = $this->preGetValue("customItems");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("customItems")->preGetData($this);

	if ($data instanceof \OpenDxp\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set customItems - Custom Items
* @param \OpenDxp\Model\DataObject\OfferToolOfferItem[] $customItems
* @return $this
*/
public function setCustomItems(?array $customItems): static
{
	/** @var \OpenDxp\Model\DataObject\ClassDefinition\Data\ManyToManyObjectRelation $fd */
	$fd = $this->getClass()->getFieldDefinition("customItems");
	$hideUnpublished = \OpenDxp\Model\DataObject\Concrete::getHideUnpublished();
	\OpenDxp\Model\DataObject\Concrete::setHideUnpublished(false);
	$currentData = $this->getCustomItems();
	\OpenDxp\Model\DataObject\Concrete::setHideUnpublished($hideUnpublished);
	$isEqual = $fd->isEqual($currentData, $customItems);
	if (!$isEqual) {
		$this->markFieldDirty("customItems", true);
	}
	$this->customItems = $fd->preSetData($this, $customItems);
	return $this;
}

}

