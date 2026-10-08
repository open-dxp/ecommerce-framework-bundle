<?php

declare(strict_types=1);

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\Tests\Application\Model;

use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractCategory;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractProduct;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem\PriceSystemInterface;

/**
 * A product that is never saved. Its price system decides what it costs.
 */
final class MockProduct extends AbstractProduct
{
    /**
     * @param list<AbstractCategory> $productCategories
     */
    public function __construct(
        private readonly int $productId,
        private readonly PriceSystemInterface $priceSystem,
        private readonly array $productCategories,
        private readonly ?AbstractProduct $parentProduct,
    ) {
    }

    public function getId(): int
    {
        return $this->productId;
    }

    public function getPriceSystemImplementation(): PriceSystemInterface
    {
        return $this->priceSystem;
    }

    public function getCategories(): array
    {
        return $this->productCategories;
    }

    public function getParent(): ?AbstractProduct
    {
        return $this->parentProduct;
    }
}
