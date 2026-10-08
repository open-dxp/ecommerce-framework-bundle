<?php

declare(strict_types=1);

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\Tests\Feature\PricingManager;

use DateTime;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\Condition\CatalogCategory;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\Condition\DateRange;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\Environment;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\EnvironmentInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Tests\Application\Model\MockCategory;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractProduct;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Tests\Application\Model\MockProduct;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Tests\Factory\ProductFactory;

function productWithId(int $id): MockProduct
{
    return ProductFactory::new()
        ->withId($id)
        ->create();
}

function variant(int $id, AbstractProduct $parent): MockProduct
{
    return ProductFactory::new()
        ->withId($id)
        ->variantOf($parent)
        ->create();
}

describe('the cart amount', function () {
    it('holds for a cart that reaches the limit', function (float $limit, bool $holds) {
        $product = ProductFactory::new()
            ->costing(200)
            ->create();
        $environment = (new Environment())->setCart(cart($product));

        $held = cartAmountOfAtLeast($limit)->check($environment);

        expect($held)->toBe($holds);
    })->with([
        'below the subtotal' => [100, true],
        'at the subtotal' => [200, true],
        'above the subtotal' => [300, false],
    ]);

    it('never holds for a single product', function () {
        $product = ProductFactory::new()
            ->costing(200)
            ->create();
        $environment = (new Environment())
            ->setCart(cart($product))
            ->setProduct($product);

        $held = cartAmountOfAtLeast(100)->check($environment);

        expect($held)->toBeFalse();
    });

    it('never holds without a cart', function () {
        $environment = (new Environment())->setProduct(ProductFactory::createOne());

        $held = cartAmountOfAtLeast(100)->check($environment);

        expect($held)->toBeFalse();
    });
});

describe('the catalog category', function () {
    it('never holds without categories in the environment', function () {
        $condition = (new CatalogCategory())->setCategories([new MockCategory('/categories/fashion')]);

        $held = $condition->check(new Environment());

        expect($held)->toBeFalse();
    });

    it('holds for a category of the environment or one of its parents', function (array $paths, bool $holds) {
        $environment = (new Environment())->setCategories([
            new MockCategory('/categories/fashion/shoes'),
            new MockCategory('/categories/fashion/tshirts'),
        ]);
        $condition = (new CatalogCategory())->setCategories(array_map(
            static fn (string $path): MockCategory => new MockCategory($path),
            $paths,
        ));

        $held = $condition->check($environment);

        expect($held)->toBe($holds);
    })->with([
        'no category' => [[], false],
        'other categories' => [['/categories/fashion/jeans', '/categories/fashion/glasses'], false],
        'the first category' => [['/categories/fashion/jeans', '/categories/fashion/shoes'], true],
        'the second category' => [['/categories/fashion/jeans', '/categories/fashion/tshirts'], true],
        'a parent category' => [['/categories/fashion', '/categories/diy'], true],
    ]);
});

describe('the catalog product', function () {
    beforeEach(function () {
        $shirts = productWithId(450);
        $jeans = productWithId(350);
        $this->cart = cart(
            variant(451, $shirts),
            variant(452, $shirts),
            variant(356, $jeans),
            productWithId(981),
        );
    });

    it('never holds without products', function () {
        $held = catalogProduct()->check(new Environment());

        expect($held)->toBeFalse();
    });

    it('never holds without a product in the environment', function () {
        $held = catalogProduct(450, 999)->check(new Environment());

        expect($held)->toBeFalse();
    });

    it('holds for the product of the environment', function () {
        $environment = (new Environment())
            ->setCart($this->cart)
            ->setProduct(productWithId(999));

        $held = catalogProduct(450, 999)->check($environment);

        expect($held)->toBeTrue();
    });

    it('looks only at the product of the environment for a product price', function () {
        $environment = (new Environment())
            ->setCart($this->cart)
            ->setProduct(productWithId(1));

        $held = catalogProduct(450, 999)->check($environment);

        expect($held)->toBeFalse();
    });

    it('also looks at the products in the cart and their parents for a cart price', function (array $ids, bool $holds) {
        $environment = (new Environment())
            ->setCart($this->cart)
            ->setProduct(productWithId(1))
            ->setExecutionMode(EnvironmentInterface::EXECUTION_MODE_CART);

        $held = catalogProduct(...$ids)->check($environment);

        expect($held)->toBe($holds);
    })->with([
        'the parent of a product in the cart' => [[450, 999], true],
        'no product of the cart' => [[888, 999], false],
    ]);
});

describe('the date range', function () {
    it('never holds without dates', function () {
        $held = (new DateRange())->check(new Environment());

        expect($held)->toBeFalse();
    });

    it('holds only between its start and its end', function (string $starting, string $ending, bool $holds) {
        $range = new DateRange();
        $range->setStarting(new DateTime($starting));
        $range->setEnding(new DateTime($ending));

        $held = $range->check(new Environment());

        expect($held)->toBe($holds);
    })->with([
        'around today' => ['-1 day', '+1 day', true],
        'in the past' => ['-2 days', '-1 day', false],
        'in the future' => ['+1 day', '+2 days', false],
        'with the end before the start' => ['+1 day', '-1 day', false],
    ]);
});
