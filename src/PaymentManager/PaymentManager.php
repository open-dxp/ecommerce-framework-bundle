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

use OpenDxp\Bundle\EcommerceFrameworkBundle\PaymentManager\Exception\ProviderNotFoundException;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PaymentManager\V7\Payment\PaymentInterface;
use Psr\Container\ContainerInterface as PsrContainerInterface;

class PaymentManager implements PaymentManagerInterface
{
    public function __construct(private readonly PsrContainerInterface $providers, protected array $providerTypes)
    {
    }

    public function getProviderTypes(): array
    {
        return $this->providerTypes;
    }

    public function getProvider(string $name): PaymentInterface
    {
        if (!$this->providers->has($name)) {
            throw new ProviderNotFoundException(sprintf(
                'The payment provider "%s" is not registered',
                $name
            ));
        }

        return $this->providers->get($name);
    }
}
