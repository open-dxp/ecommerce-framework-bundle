<?php

declare(strict_types=1);

use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractProduct;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\Action\CartDiscount;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\Action\FreeShipping;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\Action\Gift;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\Action\ProductDiscount;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\ActionInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\Condition\Bracket;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\Condition\CartAmount;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\Condition\CatalogProduct;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\ConditionInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\PricingManager;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\Rule;
use OpenDxp\TestFoundation\Container;

/**
 * A pricing manager that applies the given rules instead of the rules stored in the database.
 */
function pricingManager(Rule ...$rules): PricingManager
{
    return new class(array_values($rules)) extends PricingManager {
        /**
         * @param list<Rule> $givenRules
         */
        public function __construct(private readonly array $givenRules)
        {
            parent::__construct(
                Container::parameter('opendxp_ecommerce.pricing_manager.condition_mapping'),
                Container::parameter('opendxp_ecommerce.pricing_manager.action_mapping'),
            );
        }

        public function getValidRules(): array
        {
            return $this->givenRules;
        }
    };
}

/**
 * An active rule that runs the actions when the condition holds, or always without a condition.
 */
function rule(ActionInterface|array $actions, ?ConditionInterface $condition = null): Rule
{
    $rule = new Rule();
    $rule->setName(uniqid('rule_'));
    $rule->setActive(true);
    $rule->setActions(is_array($actions) ? $actions : [$actions]);

    if ($condition !== null) {
        $rule->setCondition($condition);
    }

    return $rule;
}

function productDiscount(float $amount): ProductDiscount
{
    $discount = new ProductDiscount();
    $discount->setAmount($amount);

    return $discount;
}

function cartDiscount(float $amount): CartDiscount
{
    $discount = new CartDiscount();
    $discount->setAmount($amount);

    return $discount;
}

function freeShipping(): FreeShipping
{
    return new FreeShipping();
}

function gift(AbstractProduct $product): Gift
{
    return (new Gift())->setProduct($product);
}

function cartAmountOfAtLeast(float $limit): CartAmount
{
    return (new CartAmount())->setLimit($limit);
}

/**
 * A condition that holds for the products with the given ids.
 */
function catalogProduct(int ...$ids): CatalogProduct
{
    return (new CatalogProduct())->setProducts(array_map(static fn (int $id): AbstractProduct => product(0, id: $id), $ids));
}

function allOf(ConditionInterface ...$conditions): Bracket
{
    return bracket(Bracket::OPERATOR_AND, $conditions);
}

function anyOf(ConditionInterface ...$conditions): Bracket
{
    return bracket(Bracket::OPERATOR_OR, $conditions);
}

/**
 * @param list<ConditionInterface> $conditions
 */
function bracket(string $operator, array $conditions): Bracket
{
    $bracket = new Bracket();

    foreach ($conditions as $condition) {
        $bracket->addCondition($condition, $operator);
    }

    return $bracket;
}
