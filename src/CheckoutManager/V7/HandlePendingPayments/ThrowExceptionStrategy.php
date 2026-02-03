<?php
declare(strict_types=1);

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Commercial License (PCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PCL
 */

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\CheckoutManager\V7\HandlePendingPayments;

use OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Exception\PaymentNotAllowedException;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractOrder;
use OpenDxp\Bundle\EcommerceFrameworkBundle\OrderManager\V7\OrderManagerInterface;

class ThrowExceptionStrategy implements HandlePendingPaymentsStrategyInterface
{
    /**
     *
     *
     * @throws PaymentNotAllowedException
     * @throws \OpenDxp\Bundle\EcommerceFrameworkBundle\Exception\UnsupportedException
     */
    public function handlePaymentNotAllowed(AbstractOrder $order, CartInterface $cart, OrderManagerInterface $orderManager): AbstractOrder
    {
        throw new PaymentNotAllowedException(
            'Payment not allowed since there is a payment pending. Cancel payment or recreate order.',
            $order,
            $cart,
            $orderManager->orderNeedsUpdate($cart, $order)
        );
    }
}
