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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\OrderManager\Order\Listing\Filter\Search;

use OpenDxp\Bundle\EcommerceFrameworkBundle\OrderManager\Order\Listing\Filter\AbstractSearch;
use OpenDxp\Bundle\EcommerceFrameworkBundle\OrderManager\OrderListInterface;

class PaymentReference extends AbstractSearch
{
    protected function getConditionColumn(): string
    {
        return 'paymentInfo.paymentReference';
    }

    #[\Override]
    protected function getConditionValue(): string
    {
        $value = parent::getConditionValue();

        return ',' . $value . ',';
    }

    /**
     * Join paymentInfo
     */
    protected function prepareApply(OrderListInterface $orderList): void
    {
        $orderList->joinPaymentInfo();
    }
}
