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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\Tracking\Tracker\Analytics;

use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractOrder;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Tracking\CheckoutCompleteInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Tracking\ProductAction;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Tracking\Transaction;
use OpenDxp\Bundle\GoogleMarketingBundle\Tracker\Tracker as GoogleTracker;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Ecommerce extends AbstractAnalyticsTracker implements CheckoutCompleteInterface
{
    protected function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'template_prefix' => '@OpenDxpEcommerceFramework/Tracking/analytics/classic',
        ]);
    }

    /**
     * Track checkout complete
     */
    public function trackCheckoutComplete(AbstractOrder $order): void
    {
        $transaction = $this->trackingItemBuilder->buildCheckoutTransaction($order);
        $items = $this->trackingItemBuilder->buildCheckoutItems($order);

        $parameters = [];
        $parameters['transaction'] = $transaction;
        $parameters['items'] = $items;
        $parameters['calls'] = $this->buildCheckoutCompleteCalls($transaction, $items);

        $result = $this->renderTemplate('checkout_complete', $parameters);

        $this->tracker->addCodePart($result, GoogleTracker::BLOCK_AFTER_TRACK);
    }

    /**
     * @param ProductAction[] $items
     */
    protected function buildCheckoutCompleteCalls(Transaction $transaction, array $items): array
    {
        $calls = [
            $this->transformTransaction($transaction),
        ];

        foreach ($items as $item) {
            $calls[] = $this->transformProductAction($item);
        }

        return $calls;
    }

    /**
     * Transform transaction into classic analytics data array
     *
     * @note city, state, country were dropped as they were optional and never used
     */
    protected function transformTransaction(Transaction $transaction): array
    {
        return [
            '_addTrans',
            $transaction->getId(),                  // order ID - required
            $transaction->getAffiliation() ?: '',   // affiliation or store name
            round($transaction->getTotal(), 2),               // total - required
            round($transaction->getTax(), 2),                 // tax
            $transaction->getShipping(),            // shipping
        ];
    }

    /**
     * Transform product action into classic analytics data array
     */
    protected function transformProductAction(ProductAction $item): array
    {
        return [
            '_addItem',
            $item->getTransactionId(),              // transaction ID - necessary to associate item with transaction
            $item->getId(),                         // SKU/code - required
            $item->getName(),                       // product name
            $item->getCategory(),                   // category or variation
            round($item->getPrice(), 2),                      // unit price - required
            $item->getQuantity() ?: 1,              // quantity - required
        ];
    }
}
