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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\CheckoutManager\V7\HandlePendingPayments;

use OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Exception\PaymentNotAllowedException;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractOrder;
use OpenDxp\Bundle\EcommerceFrameworkBundle\OrderManager\V7\OrderManagerInterface;

class CancelPaymentOrRecreateOrderStrategy implements HandlePendingPaymentsStrategyInterface
{
    public function handlePaymentNotAllowed(AbstractOrder $order, CartInterface $cart, OrderManagerInterface $orderManager): AbstractOrder
    {
        if ($orderManager->orderNeedsUpdate($cart, $order)) {
            return $orderManager->recreateOrder($cart);
        }
        $orderAgent = $orderManager->createOrderAgent($order);
        $orderAgent->cancelStartedOrderPayment();
        if ($orderManager->cartHasPendingPayments($cart)) {
            throw new PaymentNotAllowedException(
                'There are still pending payments after started payment was cancelled. Try recreate order.',
                $order,
                $cart,
                $orderManager->orderNeedsUpdate($cart, $order)
            );
        }
        return $order;
    }
}
