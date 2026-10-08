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
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\PricingManagerInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\Rule;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Tests\Application\CartManager\MockSessionCart;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Tests\Application\Model\MockProduct;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Tests\Application\PricingManager\MockPricingManager;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Tests\Factory\ProductFactory;

function pricingManager(Rule ...$rules): MockPricingManager
{
    return new MockPricingManager(array_values($rules));
}

/**
 * @return Rule an active rule that always runs the actions
 */
function rule(ActionInterface ...$actions): Rule
{
    $rule = new Rule();
    $rule->setName(uniqid('rule_'));
    $rule->setActive(true);
    $rule->setActions(array_values($actions));

    return $rule;
}

/**
 * @return Rule an active rule that runs the actions when the condition holds
 */
function ruleWhen(ConditionInterface $condition, ActionInterface ...$actions): Rule
{
    $rule = rule(...$actions);
    $rule->setCondition($condition);

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

function gift(AbstractProduct $product): Gift
{
    return (new Gift())->setProduct($product);
}

function cartAmountOfAtLeast(float $limit): CartAmount
{
    return (new CartAmount())->setLimit($limit);
}

/**
 * @return CatalogProduct a condition that holds for the products with the given ids
 */
function catalogProduct(int ...$ids): CatalogProduct
{
    $products = array_map(
        static fn (int $id): AbstractProduct => ProductFactory::new()
            ->withId($id)
            ->create(),
        $ids,
    );

    return (new CatalogProduct())->setProducts($products);
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

/**
 * @return list<MockProduct> one product per gross price, priced by the pricing manager
 */
function productsCosting(PricingManagerInterface $pricing, int ...$grossPrices): array
{
    return ProductFactory::new()
        ->pricedBy($pricing)
        ->sequence(array_map(static fn (int $price): array => ['grossPrice' => $price], $grossPrices))
        ->create();
}

/**
 * @return MockSessionCart a cart holding one product per gross price, priced by the pricing manager
 */
function pricedCart(PricingManagerInterface $pricing, int ...$grossPrices): MockSessionCart
{
    return pricedBy(cart(...productsCosting($pricing, ...$grossPrices)), $pricing);
}
