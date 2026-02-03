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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\Exception;

class ResponseWithAbortedPaymentStateException extends UnsupportedException
{
    protected ?string $paymentState;

    public function __construct(?string $newPaymentState)
    {
        $message = 'Got response although payment state was already aborted, new payment state was ' . $newPaymentState;
        parent::__construct($message);
        $this->paymentState = $newPaymentState;
    }

    public function getPaymentState(): ?string
    {
        return $this->paymentState;
    }
}
