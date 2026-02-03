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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\Tracking\Tracker;

use OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\CheckoutManager\CheckoutStepInterface as CheckoutManagerCheckoutStepInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractOrder;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\ProductInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Tracking\CartProductActionAddInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Tracking\CartProductActionRemoveInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Tracking\CheckoutCompleteInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Tracking\CheckoutInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Tracking\CheckoutStepInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Tracking\ProductAction;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Tracking\ProductImpression;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Tracking\ProductImpressionInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Tracking\ProductViewInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Tracking\Tracker;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Tracking\TrackingCodeAwareInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Tracking\Transaction;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Type\Decimal;
use Override;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GoogleTagManager extends Tracker implements
    ProductViewInterface,
    ProductImpressionInterface,
    CartProductActionAddInterface,
    CartProductActionRemoveInterface,
    CheckoutInterface,
    CheckoutStepInterface,
    CheckoutCompleteInterface,
    TrackingCodeAwareInterface
{
    const DEFERRED_DIMENSION_IMPRESSIONS = 'impressions';

    const DEFERRED_DIMENSIONS = [
        self::DEFERRED_DIMENSION_IMPRESSIONS,
    ];

    /** @var string[] */
    protected array $trackedCodes = [];

    protected array $deferred = [];

    #[Override]
    protected function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'template_prefix' => '@OpenDxpEcommerceFramework/Tracking/analytics/tagManager',
        ]);
    }

    public function trackProductImpression(ProductInterface $product, string $list = 'default'): void
    {
        $item = $this->trackingItemBuilder->buildProductImpressionItem($product, $list);

        $this->addDeferredItem(self::DEFERRED_DIMENSION_IMPRESSIONS, $this->transformProductImpression($item));
    }

    public function trackProductView(ProductInterface $product): void
    {
        $item = $this->trackingItemBuilder->buildProductViewItem($product);

        $call = [
            'event' => 'detail',
            'ecommerce' => [
                'detail' => [
                    'products' => [
                        $this->transformProductAction($item),
                    ],
                ],
            ],
        ];

        $result = $this->renderCall($call);

        $this->trackCode($result);
    }

    public function trackCartProductActionAdd(CartInterface $cart, ProductInterface $product, float|int $quantity = 1): void
    {
        $item = $this->trackingItemBuilder->buildProductActionItem($product, $quantity);

        $productArray = $this->transformProductAction($item);

        $call = [
            'event' => 'addToCart',
            'ecommerce' => [
                'add' => [
                    'products' => [
                        $productArray,
                    ],
                ],
            ],
        ];

        $result = $this->renderCall($call);

        $this->trackCode($result);
    }

    public function trackCartProductActionRemove(CartInterface $cart, ProductInterface $product, float|int $quantity = 1): void
    {
        $item = $this->trackingItemBuilder->buildProductActionItem($product, $quantity);

        $productArray = $this->transformProductAction($item);

        $call = [
            'event' => 'removeFromCart',
            'ecommerce' => [
                'remove' => [
                    'products' => [
                        $productArray,
                    ],
                ],
            ],
        ];

        $result = $this->renderCall($call);

        $this->trackCode($result);
    }

    public function trackCheckout(CartInterface $cart): void
    {
        $items = $this->trackingItemBuilder->buildCheckoutItemsByCart($cart);

        $products = $this->transformCheckoutItems($items);

        $call = [
            'event' => 'checkout',
            'ecommerce' => [
                'checkout' => [
                    'actionField' => [
                        'step' => 1,
                    ],
                    'products' => $products,
                ],
            ],
        ];

        $result = $this->renderCall($call);

        $this->trackCode($result);
    }

    public function trackCheckoutStep(CheckoutManagerCheckoutStepInterface $step, CartInterface $cart, ?string $stepNumber = null, ?string $checkoutOption = null): void
    {
        $items = $this->trackingItemBuilder->buildCheckoutItemsByCart($cart);

        $products = $this->transformCheckoutItems($items);

        $call = [
            'event' => 'checkout',
            'ecommerce' => [
                'checkout' => [
                    'actionField' => [
                        'step' => $stepNumber,
                        'option' => $checkoutOption,
                    ],
                    'products' => $products,
                ],
            ],
        ];

        $result = $this->renderCall($call);

        $this->trackCode($result);
    }

    public function trackCheckoutComplete(AbstractOrder $order): void
    {
        $transaction = $this->trackingItemBuilder->buildCheckoutTransaction($order);
        $items = $this->trackingItemBuilder->buildCheckoutItems($order);

        $call = [
            'event' => 'purchase',
            'ecommerce' => [
                'currencyCode' => $order->getCurrency(),
                'purchase' => [
                    'actionField' => $this->transformTransaction($transaction),
                    'products' => $this->transformCheckoutItems($items),
                ],
            ],
        ];

        $result = $this->renderCall($call);

        $this->trackCode($result);
    }

    /**
     * Transform product action into data array
     */
    protected function transformProductAction(ProductAction $item): array
    {
        return $this->filterNullValues(
            ['name' => $item->getName(), 'id' => $item->getId(), 'price' => $this->formatPrice($item->getPrice()), 'brand' => $item->getBrand(), 'category' => $item->getCategory(), 'variant' => $item->getVariant(), 'quantity' => $item->getQuantity(), 'position' => $item->getPosition(), 'coupon' => $item->getCoupon(), ...$item->getAdditionalAttributes()]
        );
    }

    /**
     * Transform product action into data array
     */
    protected function transformProductImpression(ProductImpression $item): array
    {
        return $this->filterNullValues(
            ['id' => $item->getId(), 'name' => $item->getName(), 'category' => $item->getCategory(), 'brand' => $item->getBrand(), 'variant' => $item->getVariant(), 'price' => $this->formatPrice($item->getPrice()), 'list' => $item->getList(), 'position' => $item->getPosition(), ...$item->getAdditionalAttributes()]
        );
    }

    /**
     * Transform transaction into data array
     */
    protected function transformTransaction(Transaction $transaction): array
    {
        return $this->filterNullValues(
            ['id' => $transaction->getId(), 'affiliation' => $transaction->getAffiliation(), 'revenue' => $this->formatPrice($transaction->getTotal()), 'tax' => $this->formatPrice($transaction->getTax()), 'coupon' => $transaction->getCoupon(), 'shipping' => $this->formatPrice($transaction->getShipping()), ...$transaction->getAdditionalAttributes()]
        );
    }

    protected function transformCheckoutItems(array $items): array
    {
        return array_map(fn (ProductAction $item) => $this->transformProductAction($item), $items);
    }

    private function formatPrice(mixed $price): string
    {
        return is_scalar($price) ? Decimal::fromNumeric($price)->asString() : '';
    }

    private function renderCall(?array $call): string
    {
        return $this->renderTemplate('call', [
            'call' => $call,
        ]);
    }

    protected function addDeferredItem(string $dimension, array $item): void
    {
        $this->deferred[$dimension][] = $item;
    }

    protected function getDeferredItems(string $dimension): ?array
    {
        return $this->deferred[$dimension] ?? null;
    }

    protected function consolidateDeferredDimensions(): void
    {
        foreach (self::DEFERRED_DIMENSIONS as $dimension) {
            if ($items = $this->getDeferredItems($dimension)) {
                $call = [
                    'ecommerce' => [
                        $dimension => $items,
                    ],
                ];

                $result = $this->renderCall($call);

                $this->trackCode($result);
            }
        }
    }

    public function getTrackedCodes(): array
    {
        $this->consolidateDeferredDimensions();

        return $this->trackedCodes;
    }

    public function trackCode(string $code): void
    {
        $this->trackedCodes[] = $code;
    }
}
