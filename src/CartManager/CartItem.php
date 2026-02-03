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

use Exception;
use OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartItem\Dao;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Factory;
use OpenDxp\Cache\RuntimeCache;
use OpenDxp\Logger;
use OpenDxp\Model\Exception\NotFoundException;

/**
 * @method Dao getDao()
 */
class CartItem extends AbstractCartItem implements CartItemInterface
{
    protected int $sortIndex = 0;

    public function setSortIndex(int $sortIndex): void
    {
        $this->sortIndex = (int)$sortIndex;
    }

    public function getSortIndex(): int
    {
        return $this->sortIndex;
    }

    public function getCart(): ?CartInterface
    {
        if (empty($this->cart)) {
            $cartClass = '\\'.Factory::getInstance()->getCartManager()->getCartClassName();
            $this->cart = $cartClass::getById($this->cartId);
        }

        return $this->cart;
    }

    public function save(): void
    {
        $items = $this->getSubItems();
        if (!empty($this->subItems)) {
            foreach ($this->subItems as $item) {
                $item->save();
            }
        }
        $this->getDao()->save();
    }

    public static function getByCartIdItemKey(int|string $cartId, string $itemKey, string $parentKey = ''): ?CartItemInterface
    {
        $cacheKey = CartItem\Dao::TABLE_NAME . '_' . $cartId . '_' . $parentKey . $itemKey;

        try {
            $cartItem = RuntimeCache::get($cacheKey);
        } catch (Exception $e) {
            try {
                $cartItem = new static();
                $cartItem->getDao()->getByCartIdItemKey($cartId, $itemKey, $parentKey);
                $cartItem->getSubItems();
                RuntimeCache::set($cacheKey, $cartItem);
            } catch (NotFoundException $ex) {
                Logger::debug($ex->getMessage());

                return null;
            }
        }

        return $cartItem;
    }

    public static function removeAllFromCart(int|string $cartId): void
    {
        $cartItem = new static();
        $cartItem->getDao()->removeAllFromCart($cartId);
    }

    /**
     * @return CartItemInterface[]
     */
    public function getSubItems(): array
    {
        if ($this->subItems == null) {
            $this->subItems = [];

            $itemClass = get_class($this) . '\\Listing';
            if (!\OpenDxp\Tool::classExists($itemClass)) {
                $itemClass = get_class($this) . '_List';
                if (!\OpenDxp\Tool::classExists($itemClass)) {
                    throw new Exception("Class $itemClass does not exist.");
                }
            }
            $itemList = new $itemClass();
            $itemList->setCartItemClassName(get_class($this));

            $db = \OpenDxp\Db::get();
            $itemList->setCondition(
                'cartId = ' . $db->quote((string)$this->getCartId()) .
                ' AND parentItemKey = ' . $db->quote($this->getItemKey())
            );

            foreach ($itemList->getCartItems() as $item) {
                if ($item->getProduct() != null) {
                    $this->subItems[] = $item;
                } else {
                    Logger::warn('product ' . $item->getProductId() . ' not found');
                }
            }
        }

        return $this->subItems;
    }
}
