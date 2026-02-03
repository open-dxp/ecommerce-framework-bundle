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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager;

use OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartItemInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractCategory;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\CheckoutableInterface;
use OpenDxp\Bundle\PersonalizationBundle\Targeting\Model\VisitorInfo;

interface EnvironmentInterface
{
    public const EXECUTION_MODE_PRODUCT = 'product';

    public const EXECUTION_MODE_CART = 'cart';

    /**
     * @return $this
     */
    public function setCart(CartInterface $cart): static;

    public function getCart(): ?CartInterface;

    /**
     * @return $this
     */
    public function setCartItem(CartItemInterface $cartItem): static;

    public function getCartItem(): ?CartItemInterface;

    /**
     * @return $this
     */
    public function setProduct(?CheckoutableInterface $product): static;

    public function getProduct(): ?CheckoutableInterface;

    /**
     * @return $this
     */
    public function setVisitorInfo(VisitorInfo $visitorInfo): static;

    public function getVisitorInfo(): ?VisitorInfo;

    /**
     * @return $this
     */
    public function setRule(RuleInterface $rule): static;

    public function getRule(): ?RuleInterface;

    /**
     * @return $this
     */
    public function setPriceInfo(PriceInfoInterface $priceInfo): static;

    public function getPriceInfo(): ?PriceInfoInterface;

    /**
     * @return $this
     */
    public function setCategories(array $categories): static;

    /**
     * @return AbstractCategory[]
     */
    public function getCategories(): array;

    /**
     * sets execution mode of system - either product or cart
     *
     * @return $this
     */
    public function setExecutionMode(string $executionMode): static;

    /**
     * returns in with execution mode the system is - either product or cart
     */
    public function getExecutionMode(): string;

    /**
     * returns hash of environment based on its content
     */
    public function getHash(): string;
}
