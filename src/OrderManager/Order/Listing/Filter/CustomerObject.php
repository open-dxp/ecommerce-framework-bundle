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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\OrderManager\Order\Listing\Filter;

use OpenDxp\Bundle\EcommerceFrameworkBundle\OrderManager\OrderListFilterInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\OrderManager\OrderListInterface;
use OpenDxp\Model\Element\ElementInterface;

class CustomerObject implements OrderListFilterInterface
{
    protected ElementInterface $customer;

    public function __construct(ElementInterface $customer)
    {
        $this->customer = $customer;
    }

    public function apply(OrderListInterface $orderList): static
    {
        $orderList->addCondition('order.customer__id = ?', (string) $this->customer->getId());

        return $this;
    }
}
