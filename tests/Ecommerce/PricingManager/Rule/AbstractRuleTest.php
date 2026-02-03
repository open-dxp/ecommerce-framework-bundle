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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\Tests\Ecommerce\PricingManager\Rule;

use Codeception\Stub;
use PHPUnit_Framework_MockObject_Stub;
use OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartPriceCalculator;
use OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartPriceModificator\Shipping;
use OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\SessionCart;
use OpenDxp\Bundle\EcommerceFrameworkBundle\EventListener\SessionBagListener;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractProduct;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\CheckoutableInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\Currency;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem\AttributePriceInfo;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem\AttributePriceSystem;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem\Price;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem\PriceInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem\TaxManagement\TaxEntry;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\ActionInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\Condition\Bracket;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\ConditionInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\PricingManager;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\PricingManagerInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\PricingManagerLocator;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\Rule;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\RuleInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Tests\Support\Test\EcommerceTestCase;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Type\Decimal;
use OpenDxp\Model\DataObject\OnlineShopTaxClass;
use OpenDxp\Tests\Support\Helper\OpenDxp;
use TypeError;

class AbstractRuleTest extends EcommerceTestCase
{
    /**
     * @throws \Codeception\Exception\ModuleException
     */
    protected function buildPricingManager(array $rules): PricingManagerInterface
    {
        $rules = $this->buildRules($rules);

        /** @var OpenDxp $opendxpModule */
        $opendxpModule = $this->getModule('\\' . OpenDxp::class);
        $container = $opendxpModule->getContainer();

        $conditionMapping = $container->getParameter('opendxp_ecommerce.pricing_manager.condition_mapping');
        $actionMapping = $container->getParameter('opendxp_ecommerce.pricing_manager.action_mapping');
        $options = [
            'rule_class' => "OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\Rule",
            'price_info_class' => "OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\PriceInfo",
            'environment_class' => "OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\Environment",
        ];

        return Stub::construct(PricingManager::class, [$conditionMapping, $actionMapping, $options], [
            'getValidRules' => function () use ($rules) {
                return $rules;
            },
        ]);
    }

    protected function createPrice(float|int|string|Decimal $value): Price|PriceInterface
    {
        return new Price(Decimal::create($value), new Currency('EUR'));
    }

    protected function buildCartCalculator(CartInterface $cart, PricingManagerInterface $pricingManager, bool $withModificators = false): CartPriceCalculator
    {
        $calculator = new CartPriceCalculator($this->buildEnvironment(), $cart);

        if ($withModificators) {
            $shipping = new Shipping(['charge' => 10]);
            $calculator->addModificator($shipping);
        }

        $calculator->setPricingManager($pricingManager);

        return $calculator;
    }

    protected function setUpCart(PricingManagerInterface $pricingManager, bool $withModificators = false): SessionCart|CartInterface|PHPUnit_Framework_MockObject_Stub
    {
        $sessionBag = $this->buildSession()->getBag(SessionBagListener::ATTRIBUTE_BAG_CART);

        /** @var SessionCart|PHPUnit_Framework_MockObject_Stub $cart */
        $cart = Stub::construct(SessionCart::class, [], [
            'getSessionBag' => function () use ($sessionBag) {
                return $sessionBag;
            },
            'isCartReadOnly' => function () {
                return false;
            },
        ]);

        $cart->setPriceCalculator($this->buildCartCalculator($cart, $pricingManager, $withModificators));

        return $cart;
    }

    /**
     * @throws TypeError
     */
    protected function setUpProduct(int $id, float $grossPrice, PricingManagerInterface $pricingManager = null, array $categories = [], array $taxes = [], string $combinationType = TaxEntry::CALCULATION_MODE_COMBINE): CheckoutableInterface
    {
        $grossPrice = Decimal::create($grossPrice);

        $taxClass = new OnlineShopTaxClass();
        $taxEntries = new \OpenDxp\Model\DataObject\Fieldcollection();

        foreach ($taxes as $name => $tax) {
            $entry = new \OpenDxp\Model\DataObject\Fieldcollection\Data\TaxEntry();
            $entry->setPercent($tax);
            $entry->setName($name);
            $taxEntries->add($entry);
        }
        $taxClass->setTaxEntries($taxEntries);
        $taxClass->setTaxEntryCombinationType($combinationType);

        $environment = $this->buildEnvironment();

        $pricingManagers = Stub::make(PricingManagerLocator::class, [
            'getPricingManager' => function () use ($pricingManager) {
                return $pricingManager;
            },
        ]);

        $priceSystem = Stub::construct(AttributePriceSystem::class, [$pricingManagers, $environment], [
            'getTaxClassForProduct' => function () use ($taxClass) {
                return $taxClass;
            },
            'getPriceClassInstance' => function (Decimal $amount) {
                return new Price($amount, new Currency('EUR'));
            },
            'calculateAmount' => function () use ($grossPrice): Decimal {
                return $grossPrice;
            },
        ]);

        /** @var AbstractProduct|PHPUnit_Framework_MockObject_Stub $product */
        $product = Stub::construct(AbstractProduct::class, [], [
            'getId' => function () use ($id) {
                return $id;
            },
            'getPriceSystemImplementation' => function () use ($priceSystem) {
                return $priceSystem;
            },
            'getCategories' => function () use ($categories) {
                return $categories;
            },
        ]);

        return $product;
    }

    protected function doAssertions(array $ruleDefinitions, array $productDefinitions, array $tests): SessionCart|CartInterface|PHPUnit_Framework_MockObject_Stub
    {
        $pricingManager = $this->buildPricingManager($ruleDefinitions);

        $singleProductPrice = $productDefinitions['singleProduct']['price'];

        $priceInfo = new AttributePriceInfo(
            $this->createPrice($singleProductPrice),
            2,
            $this->createPrice($singleProductPrice * 2)
        );

        $priceInfo = $pricingManager->applyProductRules($priceInfo);
        $product = $this->setUpProduct($productDefinitions['singleProduct']['id'], $productDefinitions['singleProduct']['price'], $pricingManager);
        $priceInfo->getEnvironment()->setProduct($product);

        $this->assertTrue($priceInfo->getPrice()->getAmount()->equals(Decimal::create($tests['productPriceSingle'])), 'check single product price: ' . $priceInfo->getPrice()->getAmount() . ' vs. ' . $tests['productPriceSingle']);
        $this->assertTrue($priceInfo->getTotalPrice()->getAmount()->equals(Decimal::create($tests['productPriceTotal'])), 'check total product price: ' . $priceInfo->getTotalPrice()->getAmount() . ' vs. ' . $tests['productPriceTotal']);

        $this->assertTrue($product->getOSPrice()->getAmount()->equals(Decimal::create($tests['productPriceSingle'])), 'check single product price via product object');

        $cart = $this->setUpCart($pricingManager, false);
        foreach ($productDefinitions['cart'] as $cartProduct) {
            $cart->addItem($this->setUpProduct($cartProduct['id'], $cartProduct['price'], $pricingManager), 1);
        }

        $this->assertTrue($cart->getPriceCalculator()->getSubTotal()->getAmount()->equals(Decimal::create($tests['cartSubTotal'])), 'check cart subtotal price: ' . $cart->getPriceCalculator()->getSubTotal()->getAmount() . ' vs ' . $tests['cartSubTotal']);
        $this->assertTrue($cart->getPriceCalculator()->getGrandTotal()->getAmount()->equals(Decimal::create($tests['cartGrandTotal'])), 'check cart total price: ' . $cart->getPriceCalculator()->getGrandTotal()->getAmount() . ' vs ' . $tests['cartGrandTotal']);

        $cart = $this->setUpCart($pricingManager, true);
        foreach ($productDefinitions['cart'] as $cartProduct) {
            $cart->addItem($this->setUpProduct($cartProduct['id'], $cartProduct['price'], $pricingManager), 1);
        }
        $this->assertTrue($cart->getPriceCalculator()->getSubTotal()->getAmount()->equals(Decimal::create($tests['cartSubTotalModificators'])), 'check cart with modificators subtotal price: ' . $cart->getPriceCalculator()->getSubTotal()->getAmount() . ' vs ' . $tests['cartSubTotalModificators']);
        $this->assertTrue($cart->getPriceCalculator()->getGrandTotal()->getAmount()->equals(Decimal::create($tests['cartGrandTotalModificators'])), 'check cart with modificators total price: ' . $cart->getPriceCalculator()->getGrandTotal()->getAmount() . ' vs ' . $tests['cartGrandTotalModificators']);

        if (array_key_exists('giftItemCount', $tests)) {
            $this->assertEquals(count($cart->getGiftItems()), $tests['giftItemCount'], 'check gift item count: ' . count($cart->getGiftItems()) . ' vs. ' . $tests['giftItemCount']);
        }

        return $cart;
    }

    protected function doAssertionsWithGiftItem(array $ruleDefinitions, array $productDefinitions, array $tests, bool $hasGiftItem): void
    {
        $this->doAssertions($ruleDefinitions, $productDefinitions, $tests);

        $pricingManager = $this->buildPricingManager($ruleDefinitions);

        $cart = $this->setUpCart($pricingManager, true);
        foreach ($productDefinitions['cart'] as $cartProduct) {
            $cart->addItem($this->setUpProduct($cartProduct['id'], $cartProduct['price'], $pricingManager), 1);
        }

        $giftItems = $cart->getGiftItems();

        if ($hasGiftItem) {
            $this->assertTrue(count($giftItems) > 0, 'Check if Cart has gift items - it should have');
        } else {
            $this->assertTrue(count($giftItems) == 0, 'Check if Cart has gift items - it should not have.');
        }
    }

    protected function doAssertionsWithShippingCosts(array $ruleDefinitions, array $productDefinitions, array $tests, bool $noShippingCosts): void
    {
        $this->doAssertions($ruleDefinitions, $productDefinitions, $tests);

        $pricingManager = $this->buildPricingManager($ruleDefinitions);

        $cart = $this->setUpCart($pricingManager, true);
        foreach ($productDefinitions['cart'] as $cartProduct) {
            $cart->addItem($this->setUpProduct($cartProduct['id'], $cartProduct['price'], $pricingManager), 1);
        }

        $modifications = $cart->getPriceCalculator()->getPriceModifications();

        if ($noShippingCosts) {
            $this->assertTrue($modifications['shipping']->getAmount()->equals(Decimal::create(0)), 'Check if cart has shipping costs - it should have');
        } else {
            $this->assertFalse($modifications['shipping']->getAmount()->equals(Decimal::create(0)), 'Check if cart has shipping costs - it should not have.');
        }
    }

    /**
     * @return ActionInterface[]
     */
    protected function buildActions(array $definitions): array
    {
        $elements = [];
        foreach ($definitions as $definition) {
            $element = new $definition['class']();

            foreach ($definition as $key => $value) {
                if ($key == 'class') {
                    continue;
                }
                $setter = 'set' . ucfirst($key);
                $element->$setter($value);
            }

            $elements[] = $element;
        }

        return $elements;
    }

    protected function buildConditions(mixed $conditionDefinitions): mixed
    {
        if ($conditionDefinitions instanceof ConditionInterface) {
            return $conditionDefinitions;
        }

        if (is_string($conditionDefinitions)) {
            return unserialize($conditionDefinitions);
        }

        if (is_array($conditionDefinitions)) {
            if ($conditionDefinitions['class'] == Bracket::class) {
                $condition = new Bracket();
                foreach ($conditionDefinitions['conditions'] as $subCondition) {
                    $condition->addCondition($this->buildConditions($subCondition['condition']), $subCondition['operator']);
                }

                return $condition;
            } else {
                $condition = new $conditionDefinitions['class']();
                foreach ($conditionDefinitions as $key => $value) {
                    if ($key == 'class') {
                        continue;
                    }
                    $setter = 'set' . ucfirst($key);
                    $condition->$setter($value);
                }

                return $condition;
            }
        }
    }

    /**
     * @return RuleInterface[]
     */
    protected function buildRules(array $ruleDefinitions): array
    {
        $rules = [];

        foreach ($ruleDefinitions as $name => $ruleDefinition) {
            $rule = new Rule();
            $rule->setName($name);
            $rule->setActive(true);
            $rule->setActions($this->buildActions($ruleDefinition['actions']));

            $condition = $this->buildConditions($ruleDefinition['condition']);
            if ($condition) {
                $rule->setCondition($condition);
            }

            //            if($ruleDefinition['condition']) {
            //                $rule->setValue("condition", $ruleDefinition['condition']);
            //            }

            $rules[] = $rule;
        }

        return $rules;
    }

    protected function mockProductForCondition(int $id, int $parentId = null): AbstractProduct
    {
        $product = $this->getMockBuilder(AbstractProduct::class)->getMock();
        $product->method('getId')->willReturn($id);

        if ($parentId) {
            $subProduct = $this->mockProduct($parentId);
            $product->method('getParent')->willReturn($subProduct);
        } else {
            $product->method('getParent')->willReturn(null);
        }

        return $product;
    }
}
