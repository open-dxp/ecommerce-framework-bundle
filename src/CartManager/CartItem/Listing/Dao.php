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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartItem\Listing;

use Exception;

/**
 * @internal
 *
 * @property \Pimcore\Bundle\EcommerceFrameworkBundle\CartManager\CartItem\Listing $model
 */
class Dao extends \OpenDxp\Model\Listing\Dao\AbstractDao
{
    protected string $className = '\OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartItem';

    public function load(): array
    {
        $items = [];
        $cartItems = $this->db->fetchAllAssociative(
            'SELECT cartid, itemKey, parentItemKey FROM '
            . \OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartItem\Dao::TABLE_NAME
            . $this->getCondition() . $this->getOrder() . $this->getOffsetLimit(),
            $this->model->getConditionVariables(),
            $this->model->getConditionVariableTypes(),
        );

        foreach ($cartItems as $item) {
            $items[] = call_user_func([$this->getClassName(), 'getByCartIdItemKey'], $item['cartid'], $item['itemKey'], $item['parentItemKey']);
        }
        $this->model->setCartItems($items);

        return $items;
    }

    public function getTotalCount(): int
    {
        try {
            return (int)$this->db->fetchOne(
                'SELECT COUNT(*) FROM `'
                . \OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartItem\Dao::TABLE_NAME
                . '`' . $this->getCondition(),
                $this->model->getConditionVariables(),
                $this->model->getConditionVariableTypes(),
            );
        } catch (Exception $e) {
            return 0;
        }
    }

    public function getTotalAmount(): int
    {
        return (int)$this->db->fetchOne(
            'SELECT SUM(count) FROM `'
            . \OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartItem\Dao::TABLE_NAME
            . '`' . $this->getCondition(),
            $this->model->getConditionVariables(),
            $this->model->getConditionVariableTypes(),
        );
    }

    public function setClassName(string $className): void
    {
        $this->className = $className;
    }

    public function getClassName(): string
    {
        return $this->className;
    }
}
