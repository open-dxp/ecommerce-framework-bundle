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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\Event\Model;

use OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractOrder;
use OpenDxp\Bundle\EcommerceFrameworkBundle\OrderManager\V7\OrderManagerInterface;
use OpenDxp\Event\Traits\ArgumentsAwareTrait;
use Symfony\Contracts\EventDispatcher\Event;

class OrderManagerEvent extends Event
{
    use ArgumentsAwareTrait;

    /**
     * OrderManagerEvent constructor.
     */
    public function __construct(protected CartInterface $cart, protected ?AbstractOrder $order, protected OrderManagerInterface $orderManager, array $arguments = [])
    {
        $this->arguments = $arguments;
    }

    public function getCart(): CartInterface
    {
        return $this->cart;
    }

    public function setCart(CartInterface $cart): void
    {
        $this->cart = $cart;
    }

    public function getOrder(): ?AbstractOrder
    {
        return $this->order;
    }

    public function setOrder(AbstractOrder $order): void
    {
        $this->order = $order;
    }

    public function getOrderManager(): OrderManagerInterface
    {
        return $this->orderManager;
    }

    public function setOrderManager(OrderManagerInterface $orderManager): void
    {
        $this->orderManager = $orderManager;
    }
}
