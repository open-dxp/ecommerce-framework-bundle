<?php

declare(strict_types=1);

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\Tests\Application\Model;

use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractCategory;

/**
 * A category that is never saved.
 */
final class MockCategory extends AbstractCategory
{
    public function __construct(private readonly string $categoryPath)
    {
    }

    public function getFullPath(): string
    {
        return $this->categoryPath;
    }
}
