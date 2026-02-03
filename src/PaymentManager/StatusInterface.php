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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\PaymentManager;

/**
 * Interface StatusInterface
 */
interface StatusInterface
{
    const STATUS_PENDING = 'paymentPending';

    const STATUS_AUTHORIZED = 'paymentAuthorized';

    const STATUS_CANCELLED = 'cancelled';

    const STATUS_CLEARED = 'committed';

    /**
     * payment reference from payment provider
     */
    public function getPaymentReference(): string;

    /**
     * pimcore internal payment id, necessary to identify payment information in order object
     */
    public function getInternalPaymentId(): string;

    /**
     * payment message provided from payment provider - e.g. error message on error
     */
    public function getMessage(): string;

    /**
     * internal opendxp order status - see also constants \OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractOrder::ORDER_STATE_*
     */
    public function getStatus(): string;

    /**
     * additional payment data
     */
    public function getData(): array;
}
