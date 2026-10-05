<?php

declare(strict_types=1);

use OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartPriceCalculator;
use OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartPriceModificator\Shipping;
use OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\SessionCart;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Environment;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\CheckoutableInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\PricingManagerInterface;
use OpenDxp\Localization\LocaleService;
use OpenDxp\Model\DataObject\OnlineShopTaxClass;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface;

/**
 * A cart that lives in no session, holding one of each product.
 */
function cart(CheckoutableInterface ...$products): SessionCart
{
    $cart = new class extends SessionCart {
        protected static function getSessionBag(): AttributeBagInterface
        {
            return new AttributeBag();
        }
    };

    $cart->setPriceCalculator(new CartPriceCalculator(new Environment(new LocaleService()), $cart));

    foreach ($products as $product) {
        $cart->addItem($product, 1);
    }

    return $cart;
}

/**
 * Lets the pricing manager apply its rules to the cart.
 */
function pricedBy(SessionCart $cart, PricingManagerInterface $pricing): SessionCart
{
    $cart->getPriceCalculator()->setPricingManager($pricing);

    return $cart;
}

/**
 * Adds a shipping charge of 10 euros to the cart.
 */
function withShipping(SessionCart $cart, ?OnlineShopTaxClass $taxClass = null): SessionCart
{
    $shipping = new Shipping(['charge' => 10]);

    if ($taxClass !== null) {
        $shipping->setTaxClass($taxClass);
    }

    $cart->getPriceCalculator()->addModificator($shipping);

    return $cart;
}
