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

class OrderSearch implements OrderListFilterInterface
{
    protected string $keyword;

    public function apply(OrderListInterface $orderList): static
    {
        // init
        $queryBuilder = $orderList->getQueryBuilder();
        $condition = <<<'SQL'
0
OR `order`.ordernumber like ?
OR `order`.comment like ?

OR `order`.customerFirstName like ?
OR `order`.customerLastName like ?
OR `order`.customerCompany like ?
OR `order`.customerStreet like ?
OR `order`.customerZip like ?
OR `order`.customerCity like ?
OR `order`.customerCountry like ?

OR `order`.deliveryFirstName like ?
OR `order`.deliveryLastName like ?
OR `order`.deliveryCompany like ?
OR `order`.deliveryStreet like ?
OR `order`.deliveryZip like ?
OR `order`.deliveryCity like ?
OR `order`.deliveryCountry like ?
SQL;

        if ($this->getKeyword()) {
            $queryBuilder->andWhere(str_replace('like ?', 'like :order_keyword', $condition))->setParameter('order_keyword', $this->getKeyword());
        }

        return $this;
    }

    public function getKeyword(): string
    {
        return $this->keyword;
    }

    public function setKeyword(string $keyword): static
    {
        $this->keyword = $keyword;

        return $this;
    }
}
