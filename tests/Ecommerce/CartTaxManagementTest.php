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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\Tests\Ecommerce;

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
use OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem\AttributePriceSystem;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem\Price;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem\TaxManagement\TaxEntry;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\PricingManager;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\PricingManagerLocator;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Tests\Support\Test\EcommerceTestCase;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Type\Decimal;
use OpenDxp\Model\DataObject\ClassDefinition;
use OpenDxp\Model\DataObject\Fieldcollection;
use OpenDxp\Model\DataObject\Fieldcollection\Data\TaxEntry as TaxEntryFieldcollection;
use OpenDxp\Model\DataObject\OnlineShopTaxClass;

class CartTaxManagementTest extends EcommerceTestCase
{
    private function buildTaxClass(array $taxes = [], string $combinationType = TaxEntry::CALCULATION_MODE_COMBINE): OnlineShopTaxClass
    {
        $taxClass = new OnlineShopTaxClass();
        $taxClass->setId((int)md5(serialize($taxes)));

        $taxEntries = new Fieldcollection();
        foreach ($taxes as $name => $tax) {
            $entry = new TaxEntryFieldcollection();
            $entry->setPercent($tax);
            $entry->setName((string)$name);
            $taxEntries->add($entry);
        }

        $taxClass->setTaxEntries($taxEntries);
        $taxClass->setTaxEntryCombinationType($combinationType);

        return $taxClass;
    }

    private function setUpProduct(float|int|string|Decimal $grossPrice, array $taxes = [], string $combinationType = TaxEntry::CALCULATION_MODE_COMBINE): CheckoutableInterface
    {
        $taxClass = $this->buildTaxClass($taxes, $combinationType);

        $environment = $this->buildEnvironment();

        $pricingManagers = Stub::make(PricingManagerLocator::class, [
            'getPricingManager' => function () {
                return new PricingManager([], []);
            },
        ]);

        $priceSystem = Stub::construct(AttributePriceSystem::class, [$pricingManagers, $environment], [
            'getTaxClassForProduct' => function () use ($taxClass) {
                return $taxClass;
            },
            'getTaxClassForPriceModification' => function () use ($taxClass) {
                return $taxClass;
            },
            'getPriceClassInstance' => function ($amount) {
                return new Price($amount, new Currency('EUR'));
            },
            'calculateAmount' => function () use ($grossPrice) {
                return Decimal::create($grossPrice);
            },
        ]);

        /** @var Stub|CheckoutableInterface $product */
        $product = Stub::construct(AbstractProduct::class, [], [
            'getId' => function () {
                return rand();
            },
            'getPriceSystemImplementation' => function () use ($priceSystem) {
                return $priceSystem;
            },
            'getCategories' => function () {
                return [];
            },
            'getClass' => function () {
                return ClassDefinition::getByName('Product');
            },
        ]);

        return $product;
    }

    private function setUpCart(): SessionCart
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

        return $cart;
    }

    private function setUpCartCalculator(CartInterface $cart, bool $withModificators = false, array $taxes = []): CartPriceCalculator
    {
        $calculator = new CartPriceCalculator($this->buildEnvironment(), $cart);

        if ($withModificators) {
            $shipping = new Shipping(['charge' => 10]);
            $shipping->setTaxClass($this->buildTaxClass($taxes));
            $calculator->addModificator($shipping);
        }

        return $calculator;
    }

    public function testCartWithoutTaxEntries(): void
    {
        $product = $this->setUpProduct(100);
        $product2 = $this->setUpProduct(50);

        $cart = $this->setUpCart();
        $cart->addItem($product, 2);
        $cart->addItem($product2, 1);

        $items = $cart->getItems();

        $this->assertEquals(2, count($items), 'item count');
        $this->assertEquals(3, $cart->getItemAmount(), 'item amount');

        $calculator = $this->setUpCartCalculator($cart);

        $subTotal = $calculator->getSubTotal();
        $grandTotal = $calculator->getGrandTotal();

        $this->assertEquals(250, $subTotal->getGrossAmount()->asNumeric(), 'subtotal gross');
        $this->assertEquals(250, $subTotal->getNetAmount()->asNumeric(), 'subtotal net');

        $this->assertEquals(250, $grandTotal->getGrossAmount()->asNumeric(), 'grandtotal gross');
        $this->assertEquals(250, $grandTotal->getNetAmount()->asNumeric(), 'grandtotal net');
    }

    public function testCartWithTaxEntriesCombine(): void
    {
        $product = $this->setUpProduct(100, [
            '1' => 10,
            '2' => 15,
        ], TaxEntry::CALCULATION_MODE_COMBINE);

        $product2 = $this->setUpProduct(50, [
            '1' => 10,
        ], TaxEntry::CALCULATION_MODE_COMBINE);

        $cart = $this->setUpCart();
        $cart->addItem($product, 2);
        $cart->addItem($product2, 1);

        $items = $cart->getItems();

        $this->assertEquals(2, count($items), 'item count');
        $this->assertEquals(3, $cart->getItemAmount(), 'item amount');

        $calculator = $this->setUpCartCalculator($cart);
        $subTotal = $calculator->getSubTotal();
        $grandTotal = $calculator->getGrandTotal();

        $this->assertSame('250.0000', $subTotal->getGrossAmount()->asString(), 'subtotal gross');
        $this->assertSame('205.4545', $subTotal->getNetAmount()->asString(), 'subtotal net');

        $taxEntries = $subTotal->getTaxEntries();

        $this->assertEquals(10, $taxEntries['1-10']->getPercent(), 'subtotal taxentry 1 percent');
        $this->assertSame('20.5455', $taxEntries['1-10']->getAmount()->asString(), 'subtotal taxentry 1 amount');
        $this->assertEquals(15, $taxEntries['2-15']->getPercent(), 'subtotal taxentry 2 percent');
        $this->assertSame('24.0000', $taxEntries['2-15']->getAmount()->asString(), 'subtotal taxentry 2 amount');

        $this->assertSame('250.0000', $grandTotal->getGrossAmount()->asString(), 'grandtotal gross');
        $this->assertSame('205.4545', $grandTotal->getNetAmount()->asString(), 'grandtotal net');

        $taxEntries = $grandTotal->getTaxEntries();

        $this->assertEquals(10, $taxEntries['1-10']->getPercent(), 'grandtotal taxentry 1 percent');
        $this->assertSame('20.5455', $taxEntries['1-10']->getAmount()->asString(), 'grandtotal taxentry 1 amount');
        $this->assertEquals(15, $taxEntries['2-15']->getPercent(), 'grandtotal taxentry 2 percent');
        $this->assertSame('24.0000', $taxEntries['2-15']->getAmount()->asString(), 'grandtotal taxentry 2 amount');
    }

    public function testPriceSystemWithTaxEntriesOneAfterAnother(): void
    {
        $product = $this->setUpProduct(100, [
            '1' => 10,
            '2' => 15,
        ], TaxEntry::CALCULATION_MODE_ONE_AFTER_ANOTHER);

        $product2 = $this->setUpProduct(50, [
            '1' => 10,
        ], TaxEntry::CALCULATION_MODE_ONE_AFTER_ANOTHER);

        $cart = $this->setUpCart();
        $cart->addItem($product, 2);
        $cart->addItem($product2, 1);

        $items = $cart->getItems();

        $this->assertEquals(2, count($items), 'item count');
        $this->assertEquals(3, $cart->getItemAmount(), 'item amount');

        $calculator = $this->setUpCartCalculator($cart);

        $subTotal = $calculator->getSubTotal();
        $grandTotal = $calculator->getGrandTotal();

        $this->assertSame('250.0000', $subTotal->getGrossAmount()->asString(), 'subtotal gross');
        $this->assertSame('203.5572', $subTotal->getNetAmount()->asString(), 'subtotal net');

        $taxEntries = $subTotal->getTaxEntries();
        $this->assertEquals(10, $taxEntries['1-10']->getPercent(), 'subtotal taxentry 1 percent');
        $this->assertSame('20.3558', $taxEntries['1-10']->getAmount()->asString(), 'subtotal taxentry 1 amount');
        $this->assertEquals(15, $taxEntries['2-15']->getPercent(), 'subtotal taxentry 2 percent');
        $this->assertSame('26.0870', $taxEntries['2-15']->getAmount()->asString(), 'subtotal taxentry 2 amount');

        $this->assertSame('250.0000', $grandTotal->getGrossAmount()->asString(), 'grandtotal gross');
        $this->assertSame('203.5572', $grandTotal->getNetAmount()->asString(), 'grandtotal net');
        $taxEntries = $grandTotal->getTaxEntries();
        $this->assertEquals(10, $taxEntries['1-10']->getPercent(), 'grandtotal taxentry 1 percent');
        $this->assertSame('20.3558', $taxEntries['1-10']->getAmount()->asString(), 'grandtotal taxentry 1 amount');
        $this->assertEquals(15, $taxEntries['2-15']->getPercent(), 'grandtotal taxentry 2 percent');
        $this->assertSame('26.0870', $taxEntries['2-15']->getAmount()->asString(), 'grandtotal taxentry 2 amount');
    }

    public function testCartWithoutTaxEntriesWithModificators(): void
    {
        $product = $this->setUpProduct(100);
        $product2 = $this->setUpProduct(50);

        $cart = $this->setUpCart();
        $cart->addItem($product, 2);
        $cart->addItem($product2, 1);

        $items = $cart->getItems();

        $this->assertEquals(2, count($items), 'item count');
        $this->assertEquals(3, $cart->getItemAmount(), 'item amount');

        $calculator = $this->setUpCartCalculator($cart, true);
        $subTotal = $calculator->getSubTotal();
        $grandTotal = $calculator->getGrandTotal();

        $this->assertEquals(250, $subTotal->getGrossAmount()->asNumeric(), 'subtotal gross');
        $this->assertEquals(250, $subTotal->getNetAmount()->asNumeric(), 'subtotal net');

        $this->assertEquals(260, $grandTotal->getGrossAmount()->asNumeric(), 'grandtotal gross');
        $this->assertEquals(260, $grandTotal->getNetAmount()->asNumeric(), 'grandtotal net');
    }

    public function testCartWithTaxEntriesCombineWithModificators(): void
    {
        $product = $this->setUpProduct(100, ['1' => 10, '2' => 15], TaxEntry::CALCULATION_MODE_COMBINE);
        $product2 = $this->setUpProduct(50, ['1' => 10], TaxEntry::CALCULATION_MODE_COMBINE);

        $cart = $this->setUpCart();
        $cart->addItem($product, 2);
        $cart->addItem($product2, 1);

        $items = $cart->getItems();

        $this->assertEquals(2, count($items), 'item count');
        $this->assertEquals(3, $cart->getItemAmount(), 'item amount');

        $calculator = $this->setUpCartCalculator($cart, true, ['shipping' => 20]);
        $subTotal = $calculator->getSubTotal();
        $grandTotal = $calculator->getGrandTotal();

        $this->assertSame('250.0000', $subTotal->getGrossAmount()->asString(), 'subtotal gross');
        $this->assertSame('205.4545', $subTotal->getNetAmount()->asString(), 'subtotal net');
        $taxEntries = $subTotal->getTaxEntries();
        $this->assertEquals(10, $taxEntries['1-10']->getPercent(), 'subtotal taxentry 1 percent');
        $this->assertSame('20.5455', $taxEntries['1-10']->getAmount()->asString(), 'subtotal taxentry 1 amount');
        $this->assertEquals(15, $taxEntries['2-15']->getPercent(), 'subtotal taxentry 2 percent');
        $this->assertSame('24.0000', $taxEntries['2-15']->getAmount()->asString(), 'subtotal taxentry 2 amount');

        $this->assertSame('260.0000', $grandTotal->getGrossAmount()->asString(), 'grandtotal gross');
        $this->assertSame('213.7878', $grandTotal->getNetAmount()->asString(), 'grandtotal net');
        $taxEntries = $grandTotal->getTaxEntries();

        $this->assertEquals(10, $taxEntries['1-10']->getPercent(), 'grandtotal taxentry 1 percent');
        $this->assertSame('20.5455', $taxEntries['1-10']->getAmount()->asString(), 'grandtotal taxentry 1 amount');
        $this->assertEquals(15, $taxEntries['2-15']->getPercent(), 'grandtotal taxentry 2 percent');
        $this->assertSame('24.0000', $taxEntries['2-15']->getAmount()->asString(), 'grandtotal taxentry 2 amount');
        $this->assertEquals(20, $taxEntries['shipping-20']->getPercent(), 'grandtotal taxentry 3 percent');
        $this->assertSame('1.6667', $taxEntries['shipping-20']->getAmount()->asString(), 'grandtotal taxentry 3 amount');
    }

    public function testPriceSystemWithTaxEntriesOneAfterAnotherWithModificators(): void
    {
        $product = $this->setUpProduct(100, ['1' => 10, '2' => 15], TaxEntry::CALCULATION_MODE_ONE_AFTER_ANOTHER);
        $product2 = $this->setUpProduct(50, ['1' => 10], TaxEntry::CALCULATION_MODE_ONE_AFTER_ANOTHER);

        $cart = $this->setUpCart();
        $cart->addItem($product, 2);
        $cart->addItem($product2, 1);

        $items = $cart->getItems();

        $this->assertEquals(2, count($items), 'item count');
        $this->assertEquals(3, $cart->getItemAmount(), 'item amount');

        $calculator = $this->setUpCartCalculator($cart, true, ['shipping' => 20]);
        $subTotal = $calculator->getSubTotal();
        $grandTotal = $calculator->getGrandTotal();

        $this->assertSame('250.0000', $subTotal->getGrossAmount()->asString(), 'subtotal gross');
        $this->assertSame('203.5572', $subTotal->getNetAmount()->asString(), 'subtotal net');
        $taxEntries = $subTotal->getTaxEntries();
        $this->assertEquals(10, $taxEntries['1-10']->getPercent(), 'subtotal taxentry 1 percent');
        $this->assertSame('20.3558', $taxEntries['1-10']->getAmount()->asString(), 'subtotal taxentry 1 amount');
        $this->assertEquals(15, $taxEntries['2-15']->getPercent(), 'subtotal taxentry 2 percent');
        $this->assertSame('26.0870', $taxEntries['2-15']->getAmount()->asString(), 'subtotal taxentry 2 amount');

        $this->assertSame('260.0000', $grandTotal->getGrossAmount()->asString(), 'grandtotal gross');
        $this->assertSame('211.8905', $grandTotal->getNetAmount()->asString(), 'grandtotal net');
        $taxEntries = $grandTotal->getTaxEntries();

        $this->assertEquals(10, $taxEntries['1-10']->getPercent(), 'grandtotal taxentry 1 percent');
        $this->assertSame('20.3558', $taxEntries['1-10']->getAmount()->asString(), 'grandtotal taxentry 1 amount');
        $this->assertEquals(15, $taxEntries['2-15']->getPercent(), 'grandtotal taxentry 2 percent');
        $this->assertSame('26.0870', $taxEntries['2-15']->getAmount()->asString(), 'grandtotal taxentry 2 amount');

        $this->assertEquals(20, $taxEntries['shipping-20']->getPercent(), 'grandtotal taxentry 3 percent');
        $this->assertSame('1.6667', $taxEntries['shipping-20']->getAmount()->asString(), 'grandtotal taxentry 3 amount');
    }
}
