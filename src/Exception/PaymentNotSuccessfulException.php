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

use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractOrder;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PaymentManager\StatusInterface;

class PaymentNotSuccessfulException extends AbstractEcommerceException
{
    /**
     * PaymentNotSuccessfulException constructor.
     */
    public function __construct(
        protected AbstractOrder $order,
        protected StatusInterface $status,
        string $message
    ) {
        parent::__construct($message);
    }

    public function getOrder(): AbstractOrder
    {
        return $this->order;
    }

    public function getStatus(): StatusInterface
    {
        return $this->status;
    }
}
