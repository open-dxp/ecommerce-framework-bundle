<?php
declare(strict_types=1);

/**
 * OpenDXP
 *
 * This source file is licensed under the GNU General Public License version 3 (GPLv3).
 *
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) Pimcore GmbH (https://pimcore.com)
 * @copyright  Modification Copyright (c) OpenDXP (https://www.opendxp.io)
 * @license    https://www.gnu.org/licenses/gpl-3.0.html  GNU General Public License version 3 (GPLv3)
 */

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager;

use DateTime;
use OpenDxp\Bundle\EcommerceFrameworkBundle\AvailabilitySystem\AvailabilityInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractSetProduct;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractSetProductEntry;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\CheckoutableInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\MockProduct;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem\PriceInfoInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem\PriceInterface;
use OpenDxp\Model\DataObject;

abstract class AbstractCartItem extends \OpenDxp\Model\AbstractModel implements CartItemInterface
{
    /**
     * flag needed for preventing call modified on cart when loading cart from storage
     */
    protected bool $isLoading = false;

    protected ?CheckoutableInterface $product = null;

    protected ?int $productId = null;

    protected string $itemKey = '';

    protected int $count = 0;

    protected string $comment = '';

    protected string $parentItemKey = '';

    protected ?array $subItems = null;

    protected ?CartInterface $cart = null;

    protected string|int|null $cartId = null;

    /**
     * @var int|null unix timestamp
     */
    protected ?int $addedDateTimestamp = null;

    public function __construct()
    {
        $this->setAddedDate(new DateTime());
    }

    public function setCount(int $count, bool $fireModified = true): void
    {
        if ($count < 0) {
            $count = 0;
        }

        if ($this->count !== $count && $this->getCart() && !$this->isLoading && $fireModified) {
            $this->getCart()->modified();
        }
        $this->count = $count;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function setProduct(CheckoutableInterface $product, bool $fireModified = true): void
    {
        if ($this->productId !== $product->getId() && !$this->isLoading && $this->getCart() && $fireModified) {
            $this->getCart()->modified();
        }
        $this->product = $product;
        $this->productId = $product->getId();
    }

    public function getProduct(): CheckoutableInterface
    {
        if ($this->product) {
            return $this->product;
        }

        $product = DataObject::getById($this->productId);

        if ($product instanceof CheckoutableInterface) {
            $this->product = $product;
        } else {
            // actual product is not available or not checkoutable (e.g. deleted in Admin)
            $product = new MockProduct();
            $product->setId($this->productId);
            $this->product = $product;
        }

        return $this->product;
    }

    public function setCart(CartInterface $cart): void
    {
        $this->cart = $cart;
        $this->cartId = $cart->getId();
    }

    abstract public function getCart(): ?CartInterface;

    public function getCartId(): int|string|null
    {
        return $this->cartId;
    }

    public function setCartId(int|string|null $cartId): void
    {
        $this->cartId = $cartId;
    }

    public function getProductId(): ?int
    {
        if (!is_null($this->productId)) {
            return $this->productId;
        }

        return $this->getProduct()->getId();
    }

    public function setProductId(int $productId): void
    {
        if ($this->productId !== $productId && !$this->isLoading && $this->getCart()) {
            $this->getCart()->modified();
        }
        $this->productId = $productId;
        $this->product = null;
    }

    public function setParentItemKey(string $parentItemKey): void
    {
        $this->parentItemKey = $parentItemKey;
    }

    public function getParentItemKey(): string
    {
        return $this->parentItemKey;
    }

    public function setItemKey(string $itemKey): void
    {
        $this->itemKey = $itemKey;
    }

    public function getItemKey(): string
    {
        return $this->itemKey;
    }

    /**
     * @param CartItemInterface[] $subItems
     */
    public function setSubItems(array $subItems): void
    {
        $cart = $this->getCart();
        if ($cart && !$this->isLoading) {
            $cart->modified();
        }

        foreach ($subItems as $item) {
            if ($item instanceof AbstractCartItem) {
                $item->setParentItemKey($this->getItemKey());
            }
        }
        $this->subItems = $subItems;
    }

    public function getPrice(): PriceInterface
    {
        return $this->getPriceInfo()->getPrice();
    }

    public function getPriceInfo(): PriceInfoInterface
    {
        if ($this->getProduct() instanceof AbstractSetProduct) {
            $priceInfo = $this->getProduct()->getOSPriceInfo($this->getCount(), $this->getSetEntries());
        } else {
            $priceInfo = $this->getProduct()->getOSPriceInfo($this->getCount());
        }

        if ($priceInfo instanceof \OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\PriceInfoInterface) {
            $priceInfo->getEnvironment()->setCart($this->getCart());
            $priceInfo->getEnvironment()->setCartItem($this);
        }

        return $priceInfo;
    }

    public function getAvailabilityInfo(): AvailabilityInterface
    {
        if ($this->getProduct() instanceof AbstractSetProduct) {
            return $this->getProduct()->getOSAvailabilityInfo($this->getCount(), $this->getSetEntries());
        }
        return $this->getProduct()->getOSAvailabilityInfo($this->getCount());
    }

    /**
     * @return \OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractSetProductEntry[]
     */
    public function getSetEntries(): array
    {
        $products = [];
        if ($this->getSubItems()) {
            foreach ($this->getSubItems() as $item) {
                $products[] = new AbstractSetProductEntry($item->getProduct(), $item->getCount());
            }
        }

        return $products;
    }

    public function setComment(string $comment): void
    {
        $this->comment = $comment;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function getTotalPrice(): PriceInterface
    {
        return $this->getPriceInfo()->getTotalPrice();
    }

    public function setAddedDate(DateTime $date = null): void
    {
        $this->addedDateTimestamp = $date ? intval($date->format('Uu')) : null;
    }

    public function getAddedDate(): DateTime
    {
        if ($this->addedDateTimestamp) {
            return DateTime::createFromFormat('U', (string) intval($this->addedDateTimestamp / 1000000));
        }
        return null;
    }

    public function getAddedDateTimestamp(): int
    {
        return $this->addedDateTimestamp ?? 0;
    }

    public function setAddedDateTimestamp(int $time): void
    {
        $this->addedDateTimestamp = $time;
    }

    /**
     * get item name
     */
    public function getName(): string
    {
        return $this->getProduct()->getOSName();
    }

    /**
     * Flag needed for preventing call modified on cart when loading cart from storage
     * only for internal usage
     *
     *
     * @internal
     */
    public function setIsLoading(bool $isLoading): void
    {
        $this->isLoading = $isLoading;
    }

    /**
     * Sets custom properties to CartItem when provided in AbstractCart::addItem
     */
    public function setCustomProperties(array $params): void
    {
        foreach ($params as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method)) {
                $this->{$method}($value);
            }
        }
    }
}
