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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\Rule\Listing;

use Exception;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\Rule\Listing;

/**
 * @internal
 *
 * @property Listing $model
 */
class Dao extends \OpenDxp\Model\Listing\Dao\AbstractDao
{
    protected string $ruleClass = '\OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\Rule';

    public function load(): array
    {
        $rules = [];

        // load objects
        $ruleIds = $this->db->fetchFirstColumn(
            'SELECT id FROM '
            . \OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\Rule\Dao::TABLE_NAME
            . $this->getCondition() . $this->getOrder() . $this->getOffsetLimit(),
            $this->model->getConditionVariables(),
            $this->model->getConditionVariableTypes(),
        );

        foreach ($ruleIds as $id) {
            $rules[] = call_user_func([$this->getRuleClass(), 'getById'], $id);
        }

        $this->model->setRules($rules);

        return $rules;
    }

    public function setRuleClass(string $cartClass): void
    {
        $this->ruleClass = $cartClass;
    }

    public function getRuleClass(): string
    {
        return $this->ruleClass;
    }

    public function getTotalCount(): int
    {
        try {
            return (int) $this->db->fetchOne(
                'SELECT COUNT(*) FROM `'
                . \OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\Rule\Dao::TABLE_NAME
                . '`' . $this->getCondition(),
                $this->model->getConditionVariables(),
                $this->model->getConditionVariableTypes(),
            );
        } catch (Exception $e) {
            return 0;
        }
    }
}
