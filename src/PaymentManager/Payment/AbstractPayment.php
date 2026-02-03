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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\PaymentManager\Payment;

use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractOrder;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PaymentManager\V7\Payment\PaymentInterface;
use OpenDxp\Model\DataObject\Listing\Concrete;
use RuntimeException;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractPayment implements PaymentInterface
{
    protected bool $recurringPaymentEnabled = false;

    protected string $configurationKey;

    protected function processOptions(array $options): void
    {
        if (isset($options['recurring_payment_enabled'])) {
            $this->recurringPaymentEnabled = (bool) $options['recurring_payment_enabled'];
        }
    }

    protected function configureOptions(OptionsResolver $resolver): OptionsResolver
    {
        $resolver
            ->setDefined('recurring_payment_enabled')
            ->setAllowedTypes('recurring_payment_enabled', ['bool']);

        return $resolver;
    }

    public function isRecurringPaymentEnabled(): bool
    {
        return $this->recurringPaymentEnabled;
    }

    public function setRecurringPaymentSourceOrderData(AbstractOrder $sourceOrder, object $paymentBrick): void
    {
        throw new RuntimeException('setRecurringPaymentSourceOrderData not implemented for ' . get_class($this));
    }

    public function applyRecurringPaymentCondition(Concrete $orderListing, array $additionalParameters = []): void
    {
        throw new RuntimeException('applyRecurringPaymentCondition not implemented for ' . get_class($this));
    }

    public function getConfigurationKey(): string
    {
        return $this->configurationKey;
    }

    public function setConfigurationKey(string $configurationKey): void
    {
        $this->configurationKey = $configurationKey;
    }
}
