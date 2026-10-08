<?php

declare(strict_types=1);

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\Tests\Feature\Application;

use OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartManagerLocatorInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Factory;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\PricingManagerLocatorInterface;
use OpenDxp\TestFoundation\Container;

it('boots the application the bundle is tested in', function () {
    expect(Container::environment())->toBe('test');
});

it('builds a service the bundle ships', function (string $service) {
    expect(Container::get($service))->toBeInstanceOf($service);
})->with([
    'the factory' => [Factory::class],
    'the cart managers' => [CartManagerLocatorInterface::class],
    'the pricing managers' => [PricingManagerLocatorInterface::class],
]);
