<?php

declare(strict_types=1);

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\Tests\Application\PricingManager;

use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\PricingManagerInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\PricingManagerLocatorInterface;

/**
 * Hands out the same pricing manager for every tenant.
 */
final readonly class MockPricingManagerLocator implements PricingManagerLocatorInterface
{
    public function __construct(private PricingManagerInterface $pricing)
    {
    }

    public function getPricingManager(?string $tenant = null): PricingManagerInterface
    {
        return $this->pricing;
    }

    public function hasPricingManager(string $tenant): bool
    {
        return true;
    }
}
