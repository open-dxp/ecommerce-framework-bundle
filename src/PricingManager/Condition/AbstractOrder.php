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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\Condition;

use Exception;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\ConditionInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\RuleInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Type\Decimal;
use OpenDxp\Logger;
use OpenDxp\Model\DataObject\OnlineShopOrder;
use OpenDxp\Model\DataObject\OnlineShopOrderItem;

abstract class AbstractOrder implements ConditionInterface
{
    /**
     * Persistent cache for all conditions inheriting from AbstractOrder
     */
    private static array $cache = [];

    private function getData(RuleInterface $rule, string $field): mixed
    {
        if (!array_key_exists($rule->getId(), self::$cache)) {
            $query = <<<'SQL'
SELECT 1

    , priceRule.ruleId
	, count(priceRule.id) as "soldCount"
	, sum(orderItem.totalPrice) as "salesAmount"

	-- DEBUG INFOS
	, orderItem.oo_id as "orderItem"
	, `order`.orderdate

FROM object_query_%2$s as `order`

    -- ordered products
    JOIN object_relations_%2$s as orderItems
        ON( 1
            AND orderItems.fieldname = "items"
            AND orderItems.src_id = `order`.oo_id
        )

	-- order item
	JOIN object_%1$s as orderItem
		ON ( 1
    	    AND orderItem.id = orderItems.dest_id
		)

	-- add active price rules
	JOIN object_collection_PricingRule_%1$s as priceRule
		ON( 1
			AND priceRule.id = orderItem.oo_id
			AND priceRule.fieldname = "PricingRules"
			AND priceRule.ruleId = %3$s
		)

WHERE 1
    AND `order`.orderState = "committed"

LIMIT 1
SQL;

            try {
                $query = sprintf($query, OnlineShopOrderItem::classId(), OnlineShopOrder::classId(), $rule->getId());
                $conn = \OpenDxp\Db::getConnection();

                self::$cache[$rule->getId()] = $conn->fetchAssociative($query);
            } catch (Exception $e) {
                Logger::error((string) $e);
            }
        }

        return self::$cache[$rule->getId()][$field];
    }

    protected function getSoldCount(RuleInterface $rule): int
    {
        return (int)$this->getData($rule, 'soldCount');
    }

    protected function getSalesAmount(RuleInterface $rule): Decimal
    {
        return Decimal::create($this->getData($rule, 'salesAmount'));
    }
}
