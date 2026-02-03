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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\OrderManager\V7;

use Exception;
use OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Exception\UnsupportedException;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractOrder;
use OpenDxp\Bundle\EcommerceFrameworkBundle\OrderManager\OrderAgentInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\OrderManager\OrderListInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PaymentManager\StatusInterface;
use OpenDxp\Model\DataObject\Folder;

interface OrderManagerInterface
{
    public function createOrderList(): OrderListInterface;

    public function createOrderAgent(AbstractOrder $order): OrderAgentInterface;

    public function setParentOrderFolder(int|Folder $orderParentFolder): void;

    public function setOrderClass(string $classname): void;

    public function setOrderItemClass(string $classname): void;

    /**
     * Looks if order object for given cart already exists, otherwise creates it
     */
    public function getOrCreateOrderFromCart(CartInterface $cart): AbstractOrder;

    public function recreateOrder(CartInterface $cart): AbstractOrder;

    public function recreateOrderBasedOnSourceOrder(AbstractOrder $sourceOrder): AbstractOrder;

    /**
     * Looks if order object for given cart exists and returns it - it does not create it!
     */
    public function getOrderFromCart(CartInterface $cart): ?AbstractOrder;

    /**
     * Returns order based on given payment status
     */
    public function getOrderByPaymentStatus(StatusInterface $paymentStatus): ?AbstractOrder;

    /**
     * Builds order listing
     *
     *
     * @throws Exception
     */
    public function buildOrderList(): \OpenDxp\Model\DataObject\Listing\Concrete;

    /**
     * Build order item listing
     *
     *
     * @throws Exception
     */
    public function buildOrderItemList(): \OpenDxp\Model\DataObject\Listing\Concrete;

    public function cartHasPendingPayments(CartInterface $cart): bool;

    /**
     * @throws UnsupportedException
     */
    public function orderNeedsUpdate(CartInterface $cart, AbstractOrder $order): bool;
}
