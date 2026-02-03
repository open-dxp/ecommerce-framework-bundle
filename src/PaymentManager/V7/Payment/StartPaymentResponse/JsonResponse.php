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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\PaymentManager\V7\Payment\StartPaymentResponse;

use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractOrder;

class JsonResponse extends AbstractResponse
{
    protected string $jsonString;

    /**
     * JsonResponse constructor.
     */
    public function __construct(AbstractOrder $order, string $jsonString)
    {
        parent::__construct($order);
        $this->jsonString = $jsonString;
    }

    public function getJsonString(): string
    {
        return $this->jsonString;
    }
}
