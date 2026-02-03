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

class ProductType implements OrderListFilterInterface
{
    protected array $types = [];

    public function apply(OrderListInterface $orderList): static
    {
        $types = [];
        $orderList->joinOrderItemObjects();

        $db = \OpenDxp\Db::get();
        foreach ($this->getTypes() as $type) {
            $types[] = $db->quote($type);
        }
        $queryBuilder = $orderList->getQueryBuilder();
        $condition = 'orderItemObjects.className IN (' . implode(',', $types) . ')';
        $queryBuilder->andWhere($condition);

        return $this;
    }

    public function getTypes(): array
    {
        return $this->types;
    }

    public function setTypes(array $types): static
    {
        $this->types = $types;

        return $this;
    }
}
