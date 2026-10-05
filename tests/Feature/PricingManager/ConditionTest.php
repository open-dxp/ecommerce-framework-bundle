<?php

declare(strict_types=1);

use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\Condition\CatalogCategory;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\Condition\DateRange;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\Environment;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\EnvironmentInterface;

describe('the cart amount', function () {
    it('holds for a cart that reaches the limit', function (float $limit, bool $holds) {
        $environment = (new Environment())->setCart(cart(product(200)));

        expect(cartAmountOfAtLeast($limit)->check($environment))->toBe($holds);
    })->with([
        'below the subtotal' => [100, true],
        'at the subtotal' => [200, true],
        'above the subtotal' => [300, false],
    ]);

    it('never holds for a single product', function () {
        $environment = (new Environment())->setCart(cart(product(200)))->setProduct(product(200));

        expect(cartAmountOfAtLeast(100)->check($environment))->toBeFalse();
    });

    it('never holds without a cart', function () {
        $environment = (new Environment())->setProduct(product(200));

        expect(cartAmountOfAtLeast(100)->check($environment))->toBeFalse();
    });
});

describe('the catalog category', function () {
    it('never holds without categories in the environment', function () {
        $condition = (new CatalogCategory())->setCategories([category('/categories/fashion')]);

        expect($condition->check(new Environment()))->toBeFalse();
    });

    it('holds for a category of the environment or one of its parents', function (array $paths, bool $holds) {
        $environment = (new Environment())->setCategories([category('/categories/fashion/shoes'), category('/categories/fashion/tshirts')]);
        $condition = (new CatalogCategory())->setCategories(array_map(category(...), $paths));

        expect($condition->check($environment))->toBe($holds);
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
        $this->cart = cart(
            product(0, id: 451, parent: product(0, id: 450)),
            product(0, id: 452, parent: product(0, id: 450)),
            product(0, id: 356, parent: product(0, id: 350)),
            product(0, id: 981),
        );
    });

    it('never holds without products', function () {
        expect(catalogProduct()->check(new Environment()))->toBeFalse();
    });

    it('never holds without a product in the environment', function () {
        expect(catalogProduct(450, 999)->check(new Environment()))->toBeFalse();
    });

    it('holds for the product of the environment', function () {
        $environment = (new Environment())->setCart($this->cart)->setProduct(product(0, id: 999));

        expect(catalogProduct(450, 999)->check($environment))->toBeTrue();
    });

    it('looks only at the product of the environment for a product price', function () {
        $environment = (new Environment())->setCart($this->cart)->setProduct(product(0, id: 1));

        expect(catalogProduct(450, 999)->check($environment))->toBeFalse();
    });

    it('also looks at the products in the cart and their parents for a cart price', function () {
        $environment = (new Environment())->setCart($this->cart)->setProduct(product(0, id: 1))
            ->setExecutionMode(EnvironmentInterface::EXECUTION_MODE_CART);

        expect(catalogProduct(450, 999)->check($environment))->toBeTrue()
            ->and(catalogProduct(888, 999)->check($environment))->toBeFalse();
    });
});

describe('the date range', function () {
    it('never holds without dates', function () {
        expect((new DateRange())->check(new Environment()))->toBeFalse();
    });

    it('holds only between its start and its end', function (string $starting, string $ending, bool $holds) {
        $range = new DateRange();
        $range->setStarting(new DateTime($starting));
        $range->setEnding(new DateTime($ending));

        expect($range->check(new Environment()))->toBe($holds);
    })->with([
        'around today' => ['-1 day', '+1 day', true],
        'in the past' => ['-2 days', '-1 day', false],
        'in the future' => ['+1 day', '+2 days', false],
        'with the end before the start' => ['+1 day', '-1 day', false],
    ]);
});
