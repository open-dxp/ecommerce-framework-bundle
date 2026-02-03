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

use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\ConditionInterface;

interface BracketInterface extends ConditionInterface
{
    const OPERATOR_AND = 'and';

    const OPERATOR_OR = 'or';

    const OPERATOR_AND_NOT = 'and_not';

    /**
     * @param string $operator BracketInterface::OPERATOR_*
     *
     * @return $this
     */
    public function addCondition(ConditionInterface $condition, string $operator): static;

    /**
     * Returns all defined conditions with given type
     *
     *
     * @return ConditionInterface[]
     */
    public function getConditionsByType(string $typeClass): array;
}
