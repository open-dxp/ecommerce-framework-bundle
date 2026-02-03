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

class Status implements StatusInterface
{
    /**
     * @param array  $data  extended data
     */
    public function __construct(
        /**
         * pimcore internal payment id, necessary to identify payment information in order object
         */
        protected string $internalPaymentId,
        /**
         * payment reference from payment provider
         */
        protected string $paymentReference,
        /**
         * payment message provided from payment provider - e.g. error message on error
         */
        protected string $message,
        /**
         * internal opendxp order status - see also constants \OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractOrder::ORDER_STATE_*
         */
        protected string $status,
        protected array $data = []
    )
    {
    }

    public function getInternalPaymentId(): string
    {
        return $this->internalPaymentId;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getPaymentReference(): string
    {
        return $this->paymentReference;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
