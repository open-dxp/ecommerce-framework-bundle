<?php

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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartCheckoutData\Listing;

use Exception;
use OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartCheckoutData;

/**
 * @internal
 *
 * @property CartCheckoutData\Listing $model
 */
class Dao extends \OpenDxp\Model\Listing\Dao\AbstractDao
{
    public function load(): array
    {
        $items = [];

        $cartCheckoutDataItems = $this->db->fetchAllAssociative(
            'SELECT cartid, `key` FROM '
            . \OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartCheckoutData\Dao::TABLE_NAME
            . $this->getCondition() . $this->getOrder() . $this->getOffsetLimit(),
            $this->model->getConditionVariables(),
            $this->model->getConditionVariableTypes(),
        );

        foreach ($cartCheckoutDataItems as $item) {
            $items[] = \OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartCheckoutData::getByKeyCartId($item['key'], $item['cartid']);
        }
        $this->model->setCartCheckoutDataItems($items);

        return $items;
    }

    public function getTotalCount(): int
    {
        try {
            return (int)$this->db->fetchOne(
                'SELECT COUNT(*) FROM `'
                . \OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartCheckoutData\Dao::TABLE_NAME
                . '`' . $this->getCondition(),
                $this->model->getConditionVariables(),
                $this->model->getConditionVariableTypes(),
            );
        } catch (Exception $e) {
            return 0;
        }
    }
}
