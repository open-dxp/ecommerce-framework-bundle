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

use OpenDxp\Bundle\EcommerceFrameworkBundle\ComponentInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractSetProductEntry;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\CheckoutableInterface;

interface CartManagerInterface extends ComponentInterface
{
    /**
     * Returns cart class name configured in the ecommerce framework config
     *
     * Is also responsible for checking if guest cart class should be used or not,
     * by calling \OpenDxp\Bundle\EcommerceFrameworkBundle\Environment::getUseGuestCart();
     */
    public function getCartClassName(): string;

    /**
     * Adds item to given cart
     *
     * @param CheckoutableInterface $product - product to add
     * @param string|null $key            - optional key of cart where the item should be added to
     * @param string|null $itemKey   - optional item key
     * @param bool $replace          - replace item if same key already exists
     * @param array $params          - optional addtional item information
     * @param AbstractSetProductEntry[] $subProducts
     *
     * @return string - item key
     */
    public function addToCart(
        CheckoutableInterface $product,
        int $count,
        ?string $key = null,
        ?string $itemKey = null,
        bool $replace = false,
        array $params = [],
        array $subProducts = [],
        ?string $comment = null
    ): string;

    /**
     * Removes item from given cart
     *
     * @param string|null $key     - optional identification of cart in case of multi cart
     */
    public function removeFromCart(string $itemKey, ?string $key = null): void;

    /**
     * Returns cart
     *
     * @param string|null $key - optional identification of cart in case of multi cart
     */
    public function getCart(?string $key = null): CartInterface;

    /**
     * Returns cart by name
     */
    public function getCartByName(string $name): ?CartInterface;

    /**
     * Returns cart by name, if it does not exist, it will be created
     */
    public function getOrCreateCartByName(string $name): CartInterface;

    /**
     * Returns all carts
     *
     * @return CartInterface[]
     */
    public function getCarts(): array;

    /**
     * Clears given cart
     *
     * @param string|null $key - optional identification of cart in case of multi cart
     */
    public function clearCart(?string $key = null): void;

    /**
     * Creates new cart
     *
     * @param array $params - array of cart information
     *
     * @return string|int key of new created cart
     */
    public function createCart(array $params): int|string;

    /**
     * Deletes cart
     *
     * @param string|null $key - optional identification of cart in case of multi cart
     */
    public function deleteCart(?string $key = null): void;

    /**
     * Creates price calculator for given cart
     */
    public function getCartPriceCalculator(CartInterface $cart): CartPriceCalculatorInterface;

    /**
     * Resets cart manager - carts need to be reloaded after reset() is called
     */
    public function reset(): void;
}
