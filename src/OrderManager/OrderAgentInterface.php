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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\OrderManager;

use OpenDxp\Bundle\EcommerceFrameworkBundle\Exception\UnsupportedException;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractOrder;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractOrder as Order;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractOrderItem as OrderItem;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractPaymentInformation;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\Currency;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PaymentManager\StatusInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PaymentManager\V7\Payment\PaymentInterface;
use OpenDxp\Model\Element\Note;

interface OrderAgentInterface
{
    public function getOrder(): Order;

    /**
     * cancel order item and refund payment
     */
    public function itemCancel(OrderItem $item): Note;

    /**
     * start item complaint
     */
    public function itemComplaint(OrderItem $item, float $quantity): Note;

    /**
     * change order item
     */
    public function itemChangeAmount(OrderItem $item, float $amount): Note;

    /**
     * set a item state
     */
    public function itemSetState(OrderItem $item, string $state): Note;

    public function getCurrency(): Currency;

    public function hasPayment(): bool;

    public function getPaymentProvider(): PaymentInterface;

    public function setPaymentProvider(PaymentInterface $paymentProvider, AbstractOrder $sourceOrder = null): OrderAgentInterface;

    /**
     * Init payment:
     *
     * creates new payment info with INIT state
     *
     * throws exception when payment info exists
     *
     *
     * @throws UnsupportedException
     */
    public function initPayment(): AbstractPaymentInformation;

    /**
     * Starts payment:
     *
     * checks if payment info with PENDING payment exists and checks if order fingerprint has not changed
     * if true -> returns existing payment info
     * if false -> creates new payment info (and aborts existing PENDING payment infos)
     *
     *
     * @throws UnsupportedException
     */
    public function startPayment(): AbstractPaymentInformation;

    /**
     * Returns current payment info of order, or null if none exists
     */
    public function getCurrentPendingPaymentInfo(): ?AbstractPaymentInformation;

    /**
     * cancels payment for current payment info
     * - payment will be cancelled, order state will be resetted and cart will we writable again.
     *
     * -> this should be used, when user cancels payment
     *
     * only possible when payment state is PENDING, otherwise exception is thrown
     *
     *
     * @throws UnsupportedException
     */
    public function cancelStartedOrderPayment(): Order;

    public function updatePayment(StatusInterface $status): OrderAgentInterface;

    /**
     * @return Note[]
     */
    public function getFullChangeLog(): array;
}
