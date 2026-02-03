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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\VoucherService\Token\Listing;

use Exception;
use OpenDxp\Bundle\EcommerceFrameworkBundle\VoucherService\Token\Listing;

/**
 * @internal
 *
 * @property Listing $model
 */
class Dao extends \OpenDxp\Model\Listing\Dao\AbstractDao
{
    public function load(): array
    {
        $tokens = [];

        $unitIds = $this->db->fetchAllAssociative('SELECT * FROM ' .
            \OpenDxp\Bundle\EcommerceFrameworkBundle\VoucherService\Token\Dao::TABLE_NAME .
              $this->getCondition() .
              $this->getOrder() .
              $this->getOffsetLimit(),
            $this->model->getConditionVariables(),
            $this->model->getConditionVariableTypes()
        );

        foreach ($unitIds as $row) {
            $item = new \OpenDxp\Bundle\EcommerceFrameworkBundle\VoucherService\Token();
            $item->getDao()->assignVariablesToModel($row);
            $tokens[] = $item;
        }

        $this->model->setTokens($tokens);

        return $tokens;
    }

    public function getTotalCount(): int
    {
        try {
            return (int)$this->db->fetchOne(
                'SELECT COUNT(*) as amount FROM ' .
                \OpenDxp\Bundle\EcommerceFrameworkBundle\VoucherService\Token\Dao::TABLE_NAME .
                $this->getCondition(),
                $this->model->getConditionVariables(),
                $this->model->getConditionVariableTypes(),
            );
        } catch (Exception $e) {
            return 0;
        }
    }
}
