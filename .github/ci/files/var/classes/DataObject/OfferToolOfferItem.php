<?php

/**
 * Inheritance: no
 * Variants: no
 *
 * Fields Summary:
 * - product [manyToOneRelation]
 * - productNumber [input]
 * - productName [input]
 * - amount [numeric]
 * - originalTotalPrice [numeric]
 * - DiscountType [select]
 * - discount [numeric]
 * - finalTotalPrice [numeric]
 * - subItems [manyToManyObjectRelation]
 * - comment [textarea]
 * - cartItemKey [input]
 */

namespace OpenDxp\Model\DataObject;

use OpenDxp\Model\DataObject\Exception\InheritanceParentNotFoundException;
use OpenDxp\Model\DataObject\PreGetValueHookInterface;

/**
* @method static \OpenDxp\Model\DataObject\OfferToolOfferItem\Listing getList(array $config = [])
* @method static \OpenDxp\Model\DataObject\OfferToolOfferItem\Listing|\OpenDxp\Model\DataObject\OfferToolOfferItem|null getByProduct(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \OpenDxp\Model\DataObject\OfferToolOfferItem\Listing|\OpenDxp\Model\DataObject\OfferToolOfferItem|null getByProductNumber(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \OpenDxp\Model\DataObject\OfferToolOfferItem\Listing|\OpenDxp\Model\DataObject\OfferToolOfferItem|null getByProductName(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \OpenDxp\Model\DataObject\OfferToolOfferItem\Listing|\OpenDxp\Model\DataObject\OfferToolOfferItem|null getByAmount(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \OpenDxp\Model\DataObject\OfferToolOfferItem\Listing|\OpenDxp\Model\DataObject\OfferToolOfferItem|null getByOriginalTotalPrice(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \OpenDxp\Model\DataObject\OfferToolOfferItem\Listing|\OpenDxp\Model\DataObject\OfferToolOfferItem|null getByDiscountType(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \OpenDxp\Model\DataObject\OfferToolOfferItem\Listing|\OpenDxp\Model\DataObject\OfferToolOfferItem|null getByDiscount(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \OpenDxp\Model\DataObject\OfferToolOfferItem\Listing|\OpenDxp\Model\DataObject\OfferToolOfferItem|null getByFinalTotalPrice(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \OpenDxp\Model\DataObject\OfferToolOfferItem\Listing|\OpenDxp\Model\DataObject\OfferToolOfferItem|null getBySubItems(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \OpenDxp\Model\DataObject\OfferToolOfferItem\Listing|\OpenDxp\Model\DataObject\OfferToolOfferItem|null getByComment(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \OpenDxp\Model\DataObject\OfferToolOfferItem\Listing|\OpenDxp\Model\DataObject\OfferToolOfferItem|null getByCartItemKey(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
*/

class OfferToolOfferItem extends \OpenDxp\Bundle\EcommerceFrameworkBundle\OfferTool\AbstractOfferItem
{
public const FIELD_PRODUCT = 'product';
public const FIELD_PRODUCT_NUMBER = 'productNumber';
public const FIELD_PRODUCT_NAME = 'productName';
public const FIELD_AMOUNT = 'amount';
public const FIELD_ORIGINAL_TOTAL_PRICE = 'originalTotalPrice';
public const FIELD_DISCOUNT_TYPE = 'DiscountType';
public const FIELD_DISCOUNT = 'discount';
public const FIELD_FINAL_TOTAL_PRICE = 'finalTotalPrice';
public const FIELD_SUB_ITEMS = 'subItems';
public const FIELD_COMMENT = 'comment';
public const FIELD_CART_ITEM_KEY = 'cartItemKey';

protected $classId = "EF_OTOI";
protected $className = "OfferToolOfferItem";
protected $product;
protected $productNumber;
protected $productName;
protected $amount;
protected $originalTotalPrice;
protected $DiscountType;
protected $discount;
protected $finalTotalPrice;
protected $subItems;
protected $comment;
protected $cartItemKey;


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
* Get product - Produkt
* @return \OpenDxp\Model\DataObject\AbstractObject|null
*/
public function getProduct(): ?\OpenDxp\Model\Element\AbstractElement
{
	if ($this instanceof PreGetValueHookInterface && !\OpenDxp::inAdmin()) {
		$preValue = $this->preGetValue("product");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("product")->preGetData($this);

	if ($data instanceof \OpenDxp\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set product - Produkt
* @param \OpenDxp\Model\DataObject\AbstractObject|null $product
* @return $this
*/
public function setProduct(?\OpenDxp\Model\Element\AbstractElement $product): static
{
	/** @var \OpenDxp\Model\DataObject\ClassDefinition\Data\ManyToOneRelation $fd */
	$fd = $this->getClass()->getFieldDefinition("product");
	$hideUnpublished = \OpenDxp\Model\DataObject\Concrete::getHideUnpublished();
	\OpenDxp\Model\DataObject\Concrete::setHideUnpublished(false);
	$currentData = $this->getProduct();
	\OpenDxp\Model\DataObject\Concrete::setHideUnpublished($hideUnpublished);
	$isEqual = $fd->isEqual($currentData, $product);
	if (!$isEqual) {
		$this->markFieldDirty("product", true);
	}
	$this->product = $fd->preSetData($this, $product);
	return $this;
}

/**
* Get productNumber - Produktnummer
* @return string|null
*/
public function getProductNumber(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\OpenDxp::inAdmin()) {
		$preValue = $this->preGetValue("productNumber");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->productNumber;

	if ($data instanceof \OpenDxp\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set productNumber - Produktnummer
* @param string|null $productNumber
* @return $this
*/
public function setProductNumber(?string $productNumber): static
{
	$this->markFieldDirty("productNumber", true);

	$this->productNumber = $productNumber;

	return $this;
}

/**
* Get productName - Produktname
* @return string|null
*/
public function getProductName(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\OpenDxp::inAdmin()) {
		$preValue = $this->preGetValue("productName");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->productName;

	if ($data instanceof \OpenDxp\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set productName - Produktname
* @param string|null $productName
* @return $this
*/
public function setProductName(?string $productName): static
{
	$this->markFieldDirty("productName", true);

	$this->productName = $productName;

	return $this;
}

/**
* Get amount - Amount
* @return float|null
*/
public function getAmount(): ?float
{
	if ($this instanceof PreGetValueHookInterface && !\OpenDxp::inAdmin()) {
		$preValue = $this->preGetValue("amount");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->amount;

	if ($data instanceof \OpenDxp\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set amount - Amount
* @param float|null $amount
* @return $this
*/
public function setAmount(?float $amount): static
{
	/** @var \OpenDxp\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getClass()->getFieldDefinition("amount");
	$this->amount = $fd->preSetData($this, $amount);
	return $this;
}

/**
* Get originalTotalPrice - Original Total Price
* @return string|null
*/
public function getOriginalTotalPrice(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\OpenDxp::inAdmin()) {
		$preValue = $this->preGetValue("originalTotalPrice");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->originalTotalPrice;

	if ($data instanceof \OpenDxp\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set originalTotalPrice - Original Total Price
* @param string|null $originalTotalPrice
* @return $this
*/
public function setOriginalTotalPrice(?string $originalTotalPrice): static
{
	/** @var \OpenDxp\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getClass()->getFieldDefinition("originalTotalPrice");
	$this->originalTotalPrice = $fd->preSetData($this, $originalTotalPrice);
	return $this;
}

/**
* Get DiscountType - Discount Type
* @return string|null
*/
public function getDiscountType(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\OpenDxp::inAdmin()) {
		$preValue = $this->preGetValue("DiscountType");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->DiscountType;

	if ($data instanceof \OpenDxp\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set DiscountType - Discount Type
* @param string|null $DiscountType
* @return $this
*/
public function setDiscountType(?string $DiscountType): static
{
	$this->markFieldDirty("DiscountType", true);

	$this->DiscountType = $DiscountType;

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
* Get finalTotalPrice - Preis
* @return string|null
*/
public function getFinalTotalPrice(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\OpenDxp::inAdmin()) {
		$preValue = $this->preGetValue("finalTotalPrice");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->finalTotalPrice;

	if ($data instanceof \OpenDxp\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set finalTotalPrice - Preis
* @param string|null $finalTotalPrice
* @return $this
*/
public function setFinalTotalPrice(?string $finalTotalPrice): static
{
	/** @var \OpenDxp\Model\DataObject\ClassDefinition\Data\Numeric $fd */
	$fd = $this->getClass()->getFieldDefinition("finalTotalPrice");
	$this->finalTotalPrice = $fd->preSetData($this, $finalTotalPrice);
	return $this;
}

/**
* Get subItems - Subitems
* @return \OpenDxp\Model\DataObject\OnlineShopOrderItem[]
*/
public function getSubItems(): array
{
	if ($this instanceof PreGetValueHookInterface && !\OpenDxp::inAdmin()) {
		$preValue = $this->preGetValue("subItems");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("subItems")->preGetData($this);

	if ($data instanceof \OpenDxp\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set subItems - Subitems
* @param \OpenDxp\Model\DataObject\OnlineShopOrderItem[] $subItems
* @return $this
*/
public function setSubItems(?array $subItems): static
{
	/** @var \OpenDxp\Model\DataObject\ClassDefinition\Data\ManyToManyObjectRelation $fd */
	$fd = $this->getClass()->getFieldDefinition("subItems");
	$hideUnpublished = \OpenDxp\Model\DataObject\Concrete::getHideUnpublished();
	\OpenDxp\Model\DataObject\Concrete::setHideUnpublished(false);
	$currentData = $this->getSubItems();
	\OpenDxp\Model\DataObject\Concrete::setHideUnpublished($hideUnpublished);
	$isEqual = $fd->isEqual($currentData, $subItems);
	if (!$isEqual) {
		$this->markFieldDirty("subItems", true);
	}
	$this->subItems = $fd->preSetData($this, $subItems);
	return $this;
}

/**
* Get comment - Comment
* @return string|null
*/
public function getComment(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\OpenDxp::inAdmin()) {
		$preValue = $this->preGetValue("comment");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->comment;

	if ($data instanceof \OpenDxp\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set comment - Comment
* @param string|null $comment
* @return $this
*/
public function setComment(?string $comment): static
{
	$this->markFieldDirty("comment", true);

	$this->comment = $comment;

	return $this;
}

/**
* Get cartItemKey - CartItemKey
* @return string|null
*/
public function getCartItemKey(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\OpenDxp::inAdmin()) {
		$preValue = $this->preGetValue("cartItemKey");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->cartItemKey;

	if ($data instanceof \OpenDxp\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set cartItemKey - CartItemKey
* @param string|null $cartItemKey
* @return $this
*/
public function setCartItemKey(?string $cartItemKey): static
{
	$this->markFieldDirty("cartItemKey", true);

	$this->cartItemKey = $cartItemKey;

	return $this;
}

}

