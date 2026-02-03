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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\Condition;

use OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartItemInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\ConditionInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\EnvironmentInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\PriceInfoInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\RuleInterface;

class Sold extends AbstractOrder implements ConditionInterface
{
    protected int $count;

    /**
     * @var int[]
     */
    protected array $currentSoldCount = [];

    protected bool $countCart = false;

    public function check(EnvironmentInterface $environment): bool
    {
        $rule = $environment->getRule();
        if ($rule) {
            $cartUsedCount = 0;

            if ($this->isCountCart()) {
                if ($environment->getCart() && $environment->getCartItem()) {
                    // cart view
                    $cartUsedCount = $this->getCartRuleCount($environment->getCart(), $rule, $environment->getCartItem());
                } elseif (!$environment->getCart()) {
                    // product view
                    $cart = $this->getCart();
                    $cartUsedCount = $this->getCartRuleCount($cart, $rule);
                }
            }

            return ($this->getSoldCount($rule) + $cartUsedCount) < $this->getCount();
        }
        return false;
    }

    public function toJSON(): string
    {
        // basic
        $json = [
            'type' => 'Sold', 'count' => $this->getCount(), 'countCart' => $this->isCountCart(),
        ];

        return json_encode($json);
    }

    public function fromJSON(string $string): ConditionInterface
    {
        $json = json_decode($string);

        $this->setCount($json->count);
        $this->setCountCart((bool)$json->countCart);

        return $this;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function setCount(int $count): void
    {
        $this->count = $count;
    }

    public function isCountCart(): bool
    {
        return $this->countCart;
    }

    public function setCountCart(bool $countCart): static
    {
        $this->countCart = $countCart;

        return $this;
    }

    protected function getCart(): ?CartInterface
    {
        // use this in your own implementation
        return null;
    }

    /**
     * Returns a count how often the rule is already used in the cart
     */
    protected function getCartRuleCount(CartInterface $cart, RuleInterface $rule, CartItemInterface $cartItem = null): int
    {
        // init
        $counter = 0;

        foreach ($cart->getItems() as $item) {
            $rules = [];

            if ($cartItem && $item->getItemKey() == $cartItem) {
                // skip self if we are on a cartItem
            } else {
                // get rules
                $priceInfo = $item->getPriceInfo();
                if ($priceInfo instanceof PriceInfoInterface && ($cartItem && $priceInfo->hasRulesApplied() || !$cartItem instanceof \OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartItemInterface)) {
                    $rules = $priceInfo->getRules();
                }
            }

            // search for current rule
            foreach ($rules as $r) {
                if ($r->getId() == $rule->getId()) {
                    $counter++;

                    break;
                }
            }
        }

        return $counter;
    }
}
