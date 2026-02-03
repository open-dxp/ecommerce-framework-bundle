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

class SessionCartCheckoutData extends AbstractCartCheckoutData
{
    protected string|int|null $cartId = null;

    public function save(): void
    {
        throw new Exception('Not implemented, should not be needed for this cart type.');
    }

    public static function getByKeyCartId(string $key, int|string $cartId): ?AbstractCartCheckoutData
    {
        throw new Exception('Not implemented, should not be needed for this cart type.');
    }

    public static function removeAllFromCart(int|string $cartId): void
    {
        $checkoutDataItem = new self();
        $cart = $checkoutDataItem->getCart();
        if ($cart instanceof SessionCart) {
            $cart->checkoutData = [];
        }
    }

    #[\Override]
    public function setCart(CartInterface $cart): void
    {
        $this->cart = $cart;
        $this->cartId = $cart->getId();
    }

    #[\Override]
    public function getCart(): ?CartInterface
    {
        if (empty($this->cart)) {
            $this->cart = SessionCart::getById($this->cartId);
        }

        return $this->cart;
    }

    #[\Override]
    public function getCartId(): int|string|null
    {
        return $this->cartId;
    }

    public function setCartId(int|string|null $cartId): void
    {
        $this->cartId = $cartId;
    }

    /**
     * @internal
     */
    #[\Override]
    public function __sleep(): array
    {
        $vars = parent::__sleep();

        $blockedVars = ['cart', 'product'];

        $finalVars = [];
        foreach ($vars as $key) {
            if (!in_array($key, $blockedVars)) {
                $finalVars[] = $key;
            }
        }

        return $finalVars;
    }
}
