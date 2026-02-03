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

class Product implements OrderListFilterInterface
{
    protected \OpenDxp\Model\DataObject\Concrete $product;

    public function __construct(\OpenDxp\Model\DataObject\Concrete $product)
    {
        $this->product = $product;
    }

    public function apply(OrderListInterface $orderList): static
    {
        $db = \OpenDxp\Db::get();
        $ids = [
            $this->product->getId() ?? 0,
        ];

        $variants = $this->product->getChildren([
            \OpenDxp\Model\DataObject\Concrete::OBJECT_TYPE_VARIANT,
        ]);

        /** @var \Pimcore\Model\DataObject\Concrete $variant */
        foreach ($variants as $variant) {
            $ids[] = $variant->getId() ?? 0;
        }

        $orderList->addCondition('orderItem.product__id IN (' . implode(',', $ids) . ')');

        return $this;
    }
}
