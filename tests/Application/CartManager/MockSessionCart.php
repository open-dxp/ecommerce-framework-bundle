<?php

declare(strict_types=1);

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\Tests\Application\CartManager;

use OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\SessionCart;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface;

/**
 * A cart that keeps its data in memory instead of a session.
 */
final class MockSessionCart extends SessionCart
{
    protected static function getSessionBag(): AttributeBagInterface
    {
        return new AttributeBag();
    }
}
