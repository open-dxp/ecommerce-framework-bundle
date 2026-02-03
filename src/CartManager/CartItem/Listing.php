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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartItem;

use OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartItemInterface;
use Override;

/**
 * @method CartItemInterface[] load()
 * @method CartItemInterface|false current()
 * @method int getTotalCount()
 * @method int getTotalAmount()
 * @method \OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartItem\Listing\Dao getDao()
 */
class Listing extends \OpenDxp\Model\Listing\AbstractListing
{
    protected array $order = ['ASC'];

    protected array $orderKey = ['`sortIndex`', '`addedDateTimestamp`'];

    #[Override]
    public function isValidOrderKey(string $key): bool
    {
        return in_array($key, ['productId', 'cartId', 'count', 'itemKey', 'addedDateTimestamp', 'sortIndex']);
    }

    /**
     * @return CartItemInterface[]
     */
    public function getCartItems(): array
    {
        return $this->getData();
    }

    /**
     * @param CartItemInterface[] $cartItems
     *
     * @return $this
     */
    public function setCartItems(array $cartItems): static
    {
        return $this->setData($cartItems);
    }

    public function setCartItemClassName(string $className): void
    {
        $this->getDao()->setClassName($className);
    }
}
