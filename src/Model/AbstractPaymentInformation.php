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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\Model;

use Carbon\Carbon;

/**
 * Abstract base class for payment information field collection
 */
abstract class AbstractPaymentInformation extends \OpenDxp\Model\DataObject\Fieldcollection\Data\AbstractData
{
    abstract public function getPaymentStart(): ?Carbon;

    abstract public function setPaymentStart(?Carbon $paymentStart): static;

    abstract public function getPaymentFinish(): ?Carbon;

    abstract public function setPaymentFinish(?Carbon $paymentFinish): static;

    abstract public function getPaymentReference(): ?string;

    abstract public function setPaymentReference(?string $paymentReference): static;

    abstract public function getPaymentState(): ?string;

    abstract public function setPaymentState(?string $paymentState): static;

    abstract public function getMessage(): ?string;

    abstract public function getProviderData(): ?string;

    abstract public function setMessage(?string $message): static;

    abstract public function getInternalPaymentId(): ?string;

    abstract public function setInternalPaymentId(?string $internalPaymentId): static;
}
