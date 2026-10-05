<?php

declare(strict_types=1);

use OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartManagerLocatorInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Factory;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\PricingManagerLocatorInterface;
use OpenDxp\TestFoundation\Container;

it('boots the application the bundle is tested in', function () {
    expect(Container::get(Factory::class))->toBeInstanceOf(Factory::class)
        ->and(Container::get(CartManagerLocatorInterface::class))->toBeInstanceOf(CartManagerLocatorInterface::class)
        ->and(Container::get(PricingManagerLocatorInterface::class))->toBeInstanceOf(PricingManagerLocatorInterface::class);
});
