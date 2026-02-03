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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\OrderManager\Order;

use InvalidArgumentException;
use OpenDxp\Bundle\EcommerceFrameworkBundle\EnvironmentInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractOrder;
use OpenDxp\Bundle\EcommerceFrameworkBundle\OrderManager\OrderAgentFactoryInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\OrderManager\OrderAgentInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\OrderManager\V7\OrderAgent;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PaymentManager\PaymentManagerInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AgentFactory implements OrderAgentFactoryInterface
{
    use LoggerAwareTrait;

    protected EnvironmentInterface $environment;

    protected PaymentManagerInterface $paymentManager;

    protected EventDispatcherInterface $eventDispatcher;

    protected string $agentClass = OrderAgent::class;

    public function __construct(
        EnvironmentInterface $environment,
        PaymentManagerInterface $paymentManager,
        EventDispatcherInterface $eventDispatcher,
        array $options = []
    ) {
        $this->environment = $environment;
        $this->paymentManager = $paymentManager;
        $this->eventDispatcher = $eventDispatcher;

        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->processOptions($resolver->resolve($options));
    }

    protected function processOptions(array $options): void
    {
        if (isset($options['agent_class'])) {
            if (!class_exists($options['agent_class'])) {
                throw new InvalidArgumentException(sprintf(
                    'Order agent class "%s" does not exist',
                    $options['agent_class']
                ));
            }

            $this->agentClass = $options['agent_class'];
        }
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('agent_class');
        $resolver->setAllowedTypes('agent_class', 'string');
    }

    public function createAgent(AbstractOrder $order): OrderAgentInterface
    {
        $class = $this->agentClass;
        $newClass = new $class($order, $this->environment, $this->paymentManager, $this->eventDispatcher);
        $newClass->setLogger($this->logger);

        return $newClass;
    }
}
