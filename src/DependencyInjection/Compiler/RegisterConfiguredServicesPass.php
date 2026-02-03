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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\DependencyInjection\Compiler;

use OpenDxp\Bundle\EcommerceFrameworkBundle\DependencyInjection\OpenDxpEcommerceFrameworkExtension;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @internal
 */
final class RegisterConfiguredServicesPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $this->registerIndexServiceWorkers($container);
        $this->registerTrackingManagerTrackers($container);
        $this->registerPaymentManagerConfiguration($container);
    }

    public function registerIndexServiceWorkers(ContainerBuilder $container): void
    {
        $workers = [];
        foreach ($container->findTaggedServiceIds('opendxp_ecommerce.index_service.worker') as $id => $tags) {
            $workers[] = new Reference($id);
        }

        $indexService = $container->findDefinition(OpenDxpEcommerceFrameworkExtension::SERVICE_ID_INDEX_SERVICE);
        $indexService->setArgument('$tenantWorkers', $workers);
    }

    public function registerTrackingManagerTrackers(ContainerBuilder $container): void
    {
        $trackers = [];

        foreach ($container->findTaggedServiceIds('opendxp_ecommerce.tracking.tracker') as $id => $tags) {
            $trackers[] = new Reference($id);
        }

        $trackingManager = $container->findDefinition(OpenDxpEcommerceFrameworkExtension::SERVICE_ID_TRACKING_MANAGER);
        $trackingManager->setArgument('$trackers', $trackers);
    }

    private function registerPaymentManagerConfiguration(ContainerBuilder $container): void
    {
        $providerTypes = [];

        foreach ($container->findTaggedServiceIds('opendxp_ecommerce.payment.provider') as $id => $tags) {
            $providerTypes[$tags[0]['key']] = $id;
        }

        $paymentManager = $container->findDefinition(OpenDxpEcommerceFrameworkExtension::SERVICE_ID_PAYMENT_MANAGER);
        $paymentManager->setArgument('$providerTypes', $providerTypes);
    }
}
