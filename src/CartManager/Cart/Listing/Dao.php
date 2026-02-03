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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\Cart\Listing;

use Exception;

/**
 * @internal
 *
 * @property \Pimcore\Bundle\EcommerceFrameworkBundle\CartManager\Cart\Listing $model
 */
class Dao extends \OpenDxp\Model\Listing\Dao\AbstractDao
{
    protected string $cartClass = '\OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\Cart';

    public function load(): array
    {
        $carts = [];
        $cartIds = $this->db->fetchFirstColumn(
            'SELECT id FROM '
            . \OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\Cart\Dao::TABLE_NAME
            . $this->getCondition() . $this->getOrder() . $this->getOffsetLimit(),
            $this->model->getConditionVariables(),
            $this->model->getConditionVariableTypes(),
        );

        foreach ($cartIds as $id) {
            $carts[] = call_user_func([$this->getCartClass(), 'getById'], $id);
        }

        $this->model->setCarts($carts);

        return $carts;
    }

    public function getTotalCount(): int
    {
        try {
            return (int) $this->db->fetchOne(
                'SELECT COUNT(*) FROM `'
                . \OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\Cart\Dao::TABLE_NAME
                . '`' . $this->getCondition(),
                $this->model->getConditionVariables(),
                $this->model->getConditionVariableTypes(),
            );
        } catch (Exception $e) {
            return 0;
        }
    }

    public function setCartClass(string $cartClass): void
    {
        $this->cartClass = $cartClass;
    }

    public function getCartClass(): string
    {
        return $this->cartClass;
    }
}
