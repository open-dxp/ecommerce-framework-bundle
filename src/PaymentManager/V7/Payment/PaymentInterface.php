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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\PaymentManager\V7\Payment;

use OpenDxp\Bundle\EcommerceFrameworkBundle\OrderManager\OrderAgentInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PaymentManager\StatusInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PaymentManager\V7\Payment\StartPaymentRequest\AbstractRequest;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PaymentManager\V7\Payment\StartPaymentResponse\StartPaymentResponseInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem\PriceInterface;

interface PaymentInterface
{
    public function getName(): string;

    /**
     * Starts payment
     */
    public function startPayment(OrderAgentInterface $orderAgent, PriceInterface $price, AbstractRequest $config): StartPaymentResponseInterface;

    /**
     * Handles response of payment provider and creates payment status object
     */
    public function handleResponse(StatusInterface|array $response): StatusInterface;

    /**
     * Returns the authorized data from payment provider
     */
    public function getAuthorizedData(): array;

    /**
     * Set authorized data from payment provider
     */
    public function setAuthorizedData(array $authorizedData): void;

    /**
     * Executes payment
     */
    public function executeDebit(?PriceInterface $price = null, ?string $reference = null): StatusInterface;

    /**
     * Executes credit
     */
    public function executeCredit(PriceInterface $price, string $reference, string $transactionId): StatusInterface;

    /**
     * returns configuration key in yml configuration file
     */
    public function getConfigurationKey(): string;
}
