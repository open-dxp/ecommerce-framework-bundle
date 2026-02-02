<?php
declare(strict_types=1);

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Commercial License (PCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PCL
 */

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\DependencyInjection;

use OpenDxp\Bundle\EcommerceFrameworkBundle\AvailabilitySystem\AvailabilitySystemLocator;
use OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartManagerLocator;
use OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartManagerLocatorInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\CheckoutManager\CheckoutManagerFactoryLocator;
use OpenDxp\Bundle\EcommerceFrameworkBundle\CheckoutManager\CheckoutManagerFactoryLocatorInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\CheckoutManager\CommitOrderProcessorLocator;
use OpenDxp\Bundle\EcommerceFrameworkBundle\CheckoutManager\CommitOrderProcessorLocatorInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\CheckoutManager\V7\HandlePendingPayments\CancelPaymentOrRecreateOrderStrategy;
use OpenDxp\Bundle\EcommerceFrameworkBundle\FilterService\FilterServiceLocator;
use OpenDxp\Bundle\EcommerceFrameworkBundle\FilterService\FilterServiceLocatorInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\OrderManager\OrderManagerLocator;
use OpenDxp\Bundle\EcommerceFrameworkBundle\OrderManager\OrderManagerLocatorInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem\PriceSystemLocator;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\PricingManagerLocator;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\PricingManagerLocatorInterface;
use OpenDxp\Bundle\ElasticsearchClientBundle\DependencyInjection\OpenDxpElasticsearchClientExtension;
use OpenDxp\Bundle\OpenSearchClientBundle\DependencyInjection\OpenDxpOpenSearchClientExtension;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

/**
 * @internal
 */
final class OpenDxpEcommerceFrameworkExtension extends ConfigurableExtension implements PrependExtensionInterface
{
    const SERVICE_ID_FACTORY = 'opendxp_ecommerce.factory';

    const SERVICE_ID_ENVIRONMENT = 'opendxp_ecommerce.environment';

    const SERVICE_ID_PAYMENT_MANAGER = 'opendxp_ecommerce.payment_manager';

    const SERVICE_ID_INDEX_SERVICE = 'opendxp_ecommerce.index_service';

    const SERVICE_ID_VOUCHER_SERVICE = 'opendxp_ecommerce.voucher_service';

    const SERVICE_ID_TOKEN_MANAGER_FACTORY = 'opendxp_ecommerce.voucher_service.token_manager_factory';

    const SERVICE_ID_OFFER_TOOL = 'opendxp_ecommerce.offer_tool';

    const SERVICE_ID_TRACKING_MANAGER = 'opendxp_ecommerce.tracking.tracking_manager';

    public function getAlias(): string
    {
        return 'opendxp_ecommerce_framework';
    }

    /**
     * The services below are defined as public as the Factory loads services via get() on
     * demand.
     *
     * {@inheritdoc}
     */
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $container->setParameter('opendxp_ecommerce.opendxp.config', $mergedConfig['opendxp']);
        $container->setParameter('opendxp_ecommerce.decimal_scale', $mergedConfig['decimal_scale']);

        $loader->load('services.yaml');
        $loader->load('event_listeners.yaml');
        $loader->load('factory.yaml');
        $loader->load('environment.yaml');
        $loader->load('cart_manager.yaml');
        $loader->load('order_manager.yaml');
        $loader->load('pricing_manager.yaml');
        $loader->load('price_systems.yaml');
        $loader->load('availability_systems.yaml');
        $loader->load('checkout_manager.yaml');
        $loader->load('payment_manager.yaml');
        $loader->load('index_service.yaml');
        $loader->load('filter_service.yaml');
        $loader->load('voucher_service.yaml');
        $loader->load('offer_tool.yaml');
        $loader->load('tracking_manager.yaml');
        $loader->load('maintenance.yaml');
        $loader->load('commands.yaml');

        $this->registerFactoryConfiguration($container, $mergedConfig['factory']);
        $this->registerEnvironmentConfiguration($container, $mergedConfig['environment']);
        $this->registerCartManagerConfiguration($container, $mergedConfig['cart_manager']);
        $this->registerOrderManagerConfiguration($container, $mergedConfig['order_manager']);
        $this->registerPricingManagerConfiguration($container, $mergedConfig['pricing_manager']);
        $this->registerPriceSystemsConfiguration($container, $mergedConfig['price_systems']);
        $this->registerAvailabilitySystemsConfiguration($container, $mergedConfig['availability_systems']);
        $this->registerCheckoutManagerConfiguration($container, $mergedConfig['checkout_manager']);
        $this->registerPaymentManagerConfiguration($container, $mergedConfig['payment_manager']);
        $this->registerIndexServiceConfig($container, $mergedConfig['index_service']);
        $this->registerFilterServiceConfig($container, $mergedConfig['filter_service']);
        $this->registerVoucherServiceConfig($container, $mergedConfig['voucher_service']);
        $this->registerOfferToolConfig($container, $mergedConfig['offer_tool']);
        $this->registerTrackingManagerConfiguration($container, $mergedConfig['tracking_manager']);
    }

    private function registerFactoryConfiguration(ContainerBuilder $container, array $config): void
    {
        $container
            ->setAlias(
                self::SERVICE_ID_FACTORY,
                $config['factory_id']
            )
            ->setPublic(true);

        $container->setParameter(
            'opendxp_ecommerce.factory.strict_tenants',
            $config['strict_tenants']
        );
    }

    private function registerEnvironmentConfiguration(ContainerBuilder $container, array $config): void
    {
        $container
            ->setAlias(
                self::SERVICE_ID_ENVIRONMENT,
                $config['environment_id']
            )
            ->setPublic(true);

        $container->setParameter('opendxp_ecommerce.environment.options', $config['options']);
    }

    private function registerCartManagerConfiguration(ContainerBuilder $container, array $config): void
    {
        $mapping = [];

        foreach ($config['tenants'] as $tenant => $tenantConfig) {
            $cartManager = new ChildDefinition($tenantConfig['cart_manager_id']);
            $cartManager->setPublic(true);

            $cartFactory = new ChildDefinition($tenantConfig['cart']['factory_id']);

            if (!empty($tenantConfig['cart']['factory_options'])) {
                $cartFactory->setArgument('$options', $tenantConfig['cart']['factory_options']);
            }

            $priceCalculatorFactory = new ChildDefinition($tenantConfig['price_calculator']['factory_id']);
            $priceCalculatorFactory->setArgument(
                '$modificatorConfig',
                $tenantConfig['price_calculator']['modificators']
            );

            if (!empty($tenantConfig['price_calculator']['factory_options'])) {
                $priceCalculatorFactory->setArgument(
                    '$options',
                    $tenantConfig['price_calculator']['factory_options']
                );
            }

            $cartManager->setArgument('$cartFactory', $cartFactory);
            $cartManager->setArgument('$cartPriceCalculatorFactory', $priceCalculatorFactory);

            $aliasName = sprintf('opendxp_ecommerce.cart_manager.%s', $tenant);
            $container->setDefinition($aliasName, $cartManager);

            $mapping[$tenant] = $aliasName;
        }

        $this->setupTenantAwareComponentLocator(
            $container,
            CartManagerLocatorInterface::class,
            $mapping,
            CartManagerLocator::class,
            [
                'opendxp_ecommerce.locator.cart_manager',
            ]
        );
    }

    private function registerOrderManagerConfiguration(ContainerBuilder $container, array $config): void
    {
        $mapping = [];

        foreach ($config['tenants'] as $tenant => $tenantConfig) {
            $orderManager = new ChildDefinition($tenantConfig['order_manager_id']);
            $orderManager->setPublic(true);

            $orderAgentFactory = new ChildDefinition($tenantConfig['order_agent']['factory_id']);

            if (!empty($tenantConfig['order_agent']['factory_options'])) {
                $orderAgentFactory->setArgument('$options', $tenantConfig['order_agent']['factory_options']);
            }

            $orderManager->setArgument('$orderAgentFactory', $orderAgentFactory);

            if (!empty($tenantConfig['options'])) {
                $orderManager->setArgument('$options', $tenantConfig['options']);
            }

            $aliasName = sprintf('opendxp_ecommerce.order_manager.%s', $tenant);
            $container->setDefinition($aliasName, $orderManager);

            $mapping[$tenant] = $aliasName;
        }

        $this->setupTenantAwareComponentLocator(
            $container,
            OrderManagerLocatorInterface::class,
            $mapping,
            OrderManagerLocator::class,
            [
                'opendxp_ecommerce.locator.order_manager',
            ]
        );
    }

    private function registerPricingManagerConfiguration(ContainerBuilder $container, array $config): void
    {
        $mapping = [];

        $container->setParameter('opendxp_ecommerce.pricing_manager.condition_mapping', $config['conditions']);
        $container->setParameter('opendxp_ecommerce.pricing_manager.action_mapping', $config['actions']);

        foreach ($config['tenants'] as $tenant => $tenantConfig) {
            $pricingManager = new ChildDefinition($tenantConfig['pricing_manager_id']);
            $pricingManager->setAutowired(true);

            if (!empty($tenantConfig['pricing_manager_options'])) {
                $pricingManager->setArgument('$options', $tenantConfig['pricing_manager_options']);
            }

            $pricingManager->addMethodCall('setEnabled', [$tenantConfig['enabled']]);

            $aliasName = sprintf('opendxp_ecommerce.pricing_manager.%s', $tenant);
            $container->setDefinition($aliasName, $pricingManager);

            $mapping[$tenant] = $aliasName;
        }

        $this->setupTenantAwareComponentLocator(
            $container,
            PricingManagerLocatorInterface::class,
            $mapping,
            PricingManagerLocator::class,
            [
                'opendxp_ecommerce.locator.pricing_manager',
            ]
        );
    }

    private function registerPriceSystemsConfiguration(ContainerBuilder $container, array $config): void
    {
        $mapping = [];

        foreach ($config as $name => $cfg) {
            $aliasName = sprintf('opendxp_ecommerce.price_system.%s', $name);

            $container->setAlias($aliasName, $cfg['id']);
            $mapping[$name] = $aliasName;
        }

        $this->setupNameServiceComponentLocator(
            $container,
            'price_system',
            $mapping,
            PriceSystemLocator::class
        );
    }

    private function registerAvailabilitySystemsConfiguration(ContainerBuilder $container, array $config): void
    {
        $mapping = [];

        foreach ($config as $name => $cfg) {
            $aliasName = sprintf('opendxp_ecommerce.availability_system.%s', $name);

            $container->setAlias($aliasName, $cfg['id']);
            $mapping[$name] = $aliasName;
        }

        $this->setupNameServiceComponentLocator(
            $container,
            'availability_system',
            $mapping,
            AvailabilitySystemLocator::class
        );
    }

    private function registerCheckoutManagerConfiguration(ContainerBuilder $container, array $config): void
    {
        $commitOrderProcessorMapping = [];
        $checkoutManagerFactoryMapping = [];

        foreach ($config['tenants'] as $tenant => $tenantConfig) {
            $commitOrderProcessor = new ChildDefinition($tenantConfig['commit_order_processor']['id']);

            if (!empty($tenantConfig['commit_order_processor']['options'])) {
                $commitOrderProcessor->setArgument('$options', $tenantConfig['commit_order_processor']['options']);
            }

            $checkoutManagerFactory = new ChildDefinition($tenantConfig['factory_id']);
            $checkoutManagerFactory->setArguments([
                '$checkoutStepDefinitions' => $tenantConfig['steps'],
            ]);

            $paymentStrategyLocatorMapping = [];
            if (!empty($tenantConfig['factory_options'])) {
                $factoryConfig = $tenantConfig['factory_options'];

                $locatorMapping = [];
                if ($factoryConfig['handle_pending_payments_strategy'] ?? false) {
                    $paymentStrategyLocatorMapping[$factoryConfig['handle_pending_payments_strategy']] = $factoryConfig['handle_pending_payments_strategy'];
                }

                $checkoutManagerFactory->setArgument('$options', $factoryConfig);
            }

            if (empty($paymentStrategyLocatorMapping)) {
                $paymentStrategyLocatorMapping[CancelPaymentOrRecreateOrderStrategy::class] = CancelPaymentOrRecreateOrderStrategy::class;
            }
            $checkoutManagerFactory->setArgument('$handlePendingPaymentStrategyLocator', $this->setupServiceLocator($container, 'opendxp_ecommerce.checkout_manager.handle_pending_payments_strategy_locator', $paymentStrategyLocatorMapping));

            if (null !== $tenantConfig['payment']['provider']) {
                $checkoutManagerFactory->setArgument('$paymentProvider', new Reference(sprintf(
                    'opendxp_ecommerce.payment_manager.provider.%s',
                    $tenantConfig['payment']['provider']
                )));
            }

            $commitOrderProcessorAliasName = sprintf(
                'opendxp_ecommerce.checkout_manager.%s.commit_order_processor',
                $tenant
            );

            $checkoutManagerFactoryAliasName = sprintf(
                'opendxp_ecommerce.checkout_manager.%s.factory',
                $tenant
            );

            $container->setDefinition($commitOrderProcessorAliasName, $commitOrderProcessor);
            $container->setDefinition($checkoutManagerFactoryAliasName, $checkoutManagerFactory);

            $commitOrderProcessorMapping[$tenant] = $commitOrderProcessorAliasName;
            $checkoutManagerFactoryMapping[$tenant] = $checkoutManagerFactoryAliasName;
        }

        $this->setupTenantAwareComponentLocator(
            $container,
            CommitOrderProcessorLocatorInterface::class,
            $commitOrderProcessorMapping,
            CommitOrderProcessorLocator::class,
            [
                'opendxp_ecommerce.locator.checkout_manager.commit_order_processor',
            ]
        );

        $this->setupTenantAwareComponentLocator(
            $container,
            CheckoutManagerFactoryLocatorInterface::class,
            $checkoutManagerFactoryMapping,
            CheckoutManagerFactoryLocator::class,
            [
                'opendxp_ecommerce.locator.checkout_manager.factory',
            ]
        );
    }

    private function registerPaymentManagerConfiguration(ContainerBuilder $container, array $config): void
    {
        $container
            ->setAlias(
                self::SERVICE_ID_PAYMENT_MANAGER,
                $config['payment_manager_id']
            )
            ->setPublic(true);

        $mapping = [];

        foreach ($config['providers'] as $name => $providerConfig) {
            if (!isset($providerConfig['profiles'][$providerConfig['profile']])) {
                throw new InvalidConfigurationException(sprintf(
                    'Payment provider "%s" is configured to use profile "%s", but profile is not defined',
                    $name,
                    $providerConfig['profile']
                ));
            }

            $profileConfig = $providerConfig['profiles'][$providerConfig['profile']];

            $provider = new ChildDefinition($providerConfig['provider_id']);
            if (!empty($profileConfig)) {
                $provider->setArgument('$options', $profileConfig);
            }
            $provider->addMethodCall('setConfigurationKey', [$name]);

            $serviceId = sprintf('opendxp_ecommerce.payment_manager.provider.%s', $name);
            $container->setDefinition($serviceId, $provider);

            $mapping[$name] = $serviceId;
        }

        $this->setupServiceLocator($container, 'payment_manager.provider', $mapping);
    }

    private function registerIndexServiceConfig(ContainerBuilder $container, array $config): void
    {
        $container
            ->setAlias(
                self::SERVICE_ID_INDEX_SERVICE,
                $config['index_service_id']
            )
            ->setPublic(true);

        $container->setParameter('opendxp_ecommerce.index_service.default_tenant', $config['default_tenant']);

        $getterIds = [];
        $interpreterIds = [];

        foreach ($config['tenants'] ?? [] as $tenant => $tenantConfig) {
            if (!$tenantConfig['enabled']) {
                continue;
            }

            $configId = sprintf('opendxp_ecommerce.index_service.%s.config', $tenant);
            $workerId = sprintf('opendxp_ecommerce.index_service.%s.worker', $tenant);

            $attributes = $tenantConfig['attributes'];

            // collect configured getters and interpreters and
            // create a locator service for each which will be used
            // from attribute factory
            foreach ($attributes as $attribute) {
                if ($attribute['getter_id']) {
                    $getterIds[$attribute['getter_id']] = $attribute['getter_id'];
                }

                if ($attribute['interpreter_id']) {
                    $interpreterIds[$attribute['interpreter_id']] = $attribute['interpreter_id'];
                }
            }

            $config = new ChildDefinition($tenantConfig['config_id']);
            $config->setArguments([
                '$tenantName' => $tenant,
                '$attributes' => $attributes,
                '$searchAttributes' => $tenantConfig['search_attributes'],
                '$filterTypes' => [],
            ]);

            if (!empty($tenantConfig['config_options'])) {
                $config->setArgument('$options', $tenantConfig['config_options']);
                $this->registerIndexServiceSynonymProviders($tenantConfig['config_options'], $config, $container);
            }

            $worker = new ChildDefinition($tenantConfig['worker_id']);
            $worker->setArgument('$tenantConfig', new Reference($configId));
            $worker->addTag('opendxp_ecommerce.index_service.worker', ['tenant' => $tenant]);

            if (!empty($tenantConfig['config_options']['es_client_name'])) {
                $worker->addMethodCall(
                    'setElasticSearchClient',
                    [new Reference(OpenDxpElasticsearchClientExtension::CLIENT_SERVICE_PREFIX . $tenantConfig['config_options']['es_client_name'])]
                );
            }

            if (!empty($tenantConfig['config_options']['opensearch_client_name'])) {
                $worker->addMethodCall(
                    'setOpenSearchClient',
                    [new Reference(OpenDxpOpenSearchClientExtension::CLIENT_SERVICE_PREFIX . $tenantConfig['config_options']['opensearch_client_name'])]
                );
            }

            $container->setDefinition($configId, $config);
            $container->setDefinition($workerId, $worker);
        }

        $this->setupServiceLocator($container, 'index_service.getters', $getterIds);
        $this->setupServiceLocator($container, 'index_service.interpreters', $interpreterIds);
    }

    /**
     * Register synonym providers and their options per tenant config.
     */
    private function registerIndexServiceSynonymProviders(
        array $tenantConfigOptions,
        Definition $config,
        ContainerBuilder $container
    ): void {
        if (!isset($tenantConfigOptions['synonym_providers'])) {
            return;
        }

        $providers = [];
        foreach ($tenantConfigOptions['synonym_providers'] as $name => $synonymProviderConfig) {
            $synonymProvider = new ChildDefinition($synonymProviderConfig['provider_id']);
            $synonymProvider->setArgument('$options', $synonymProviderConfig['options'] ?? []);
            $synonymProviderServiceId = self::SERVICE_ID_INDEX_SERVICE.'.synonym_provider.'.$name;
            $container->setDefinition($synonymProviderServiceId, $synonymProvider);
            $providers[$name] = $synonymProvider;
        }

        $config->setArgument('$synonymProviders', $providers);
    }

    private function registerFilterServiceConfig(ContainerBuilder $container, array $config): void
    {
        $mapping = [];

        foreach ($config['tenants'] ?? [] as $tenant => $tenantConfig) {
            if (!$tenantConfig['enabled']) {
                continue;
            }

            $filterTypes = [];
            foreach ($tenantConfig['filter_types'] ?? [] as $filterTypeName => $filterTypeConfig) {
                $filterType = new ChildDefinition($filterTypeConfig['filter_type_id']);
                $filterType->setArgument('$template', $filterTypeConfig['template']);

                if (!empty($filterTypeConfig['options'])) {
                    $filterType->setArgument('$options', $filterTypeConfig['options']);
                }

                $filterTypes[$filterTypeName] = $filterType;
            }

            $filterService = new ChildDefinition($tenantConfig['service_id']);
            $filterService->setArgument('$filterTypes', $filterTypes);

            $serviceId = sprintf('opendxp_ecommerce.filter_service.%s', $tenant);
            $container->setDefinition($serviceId, $filterService);

            $mapping[$tenant] = $serviceId;
        }

        $this->setupTenantAwareComponentLocator(
            $container,
            FilterServiceLocatorInterface::class,
            $mapping,
            FilterServiceLocator::class,
            [
                'opendxp_ecommerce.locator.filter_service',
            ]
        );
    }

    private function registerVoucherServiceConfig(ContainerBuilder $container, array $config): void
    {
        // voucher service options are referenced in service definition
        $container->setParameter(
            'opendxp_ecommerce.voucher_service.options',
            $config['voucher_service_options']
        );

        $container
            ->setAlias(
                self::SERVICE_ID_VOUCHER_SERVICE,
                $config['voucher_service_id']
            )
            ->setPublic(true);

        $container->setParameter(
            'opendxp_ecommerce.voucher_service.token_manager.mapping',
            $config['token_managers']['mapping']
        );

        $container
            ->setAlias(
                self::SERVICE_ID_TOKEN_MANAGER_FACTORY,
                $config['token_managers']['factory_id']
            )
            ->setPublic(true);
    }

    private function registerOfferToolConfig(ContainerBuilder $container, array $config): void
    {
        $container
            ->setAlias(
                self::SERVICE_ID_OFFER_TOOL,
                $config['service_id']
            )
            ->setPublic(true);

        $container->setParameter(
            'opendxp_ecommerce.offer_tool.order_storage.offer_class',
            $config['order_storage']['offer_class']
        );

        $container->setParameter(
            'opendxp_ecommerce.offer_tool.order_storage.offer_item_class',
            $config['order_storage']['offer_item_class']
        );

        $container->setParameter(
            'opendxp_ecommerce.offer_tool.order_storage.parent_folder_path',
            $config['order_storage']['offer_parent_path'] ?? $config['order_storage']['parent_folder_path']
        );
    }

    private function registerTrackingManagerConfiguration(ContainerBuilder $container, array $config): void
    {
        $container
            ->setAlias(
                self::SERVICE_ID_TRACKING_MANAGER,
                $config['tracking_manager_id']
            )
            ->setPublic(true);

        foreach ($config['trackers'] as $name => $trackerConfig) {
            if (!$trackerConfig['enabled']) {
                continue;
            }

            $tracker = new ChildDefinition($trackerConfig['id']);

            if (null !== $trackerConfig['item_builder_id']) {
                $tracker->setArgument('$trackingItemBuilder', new Reference($trackerConfig['item_builder_id']));
            }

            if (!empty($trackerConfig['tenants']['assortment'])) {
                $tracker->setArgument('$assortmentTenants', $trackerConfig['tenants']['assortment']);
            }
            if (!empty($trackerConfig['tenants']['checkout'])) {
                $tracker->setArgument('$checkoutTenants', $trackerConfig['tenants']['checkout']);
            }

            if (!empty($trackerConfig['options'])) {
                $tracker->setArgument('$options', $trackerConfig['options']);
            }

            $tracker->addTag('opendxp_ecommerce.tracking.tracker', ['name' => $name]);

            $container->setDefinition(sprintf('opendxp_ecommerce.tracking.tracker.%s', $name), $tracker);
        }
    }

    private function setupTenantAwareComponentLocator(ContainerBuilder $container, string $id, array $mapping, string $class, array $aliases): void
    {
        $serviceLocator = $this->setupServiceLocator($container, $id, $mapping, false);

        $container->setDefinition($id, new Definition($class, [
            $serviceLocator,
            new Reference('opendxp_ecommerce.environment'),
            $container->getParameter('opendxp_ecommerce.factory.strict_tenants'),
        ]));

        foreach ($aliases as $alias) {
            $container->setAlias($alias, $id);
        }
    }

    private function setupNameServiceComponentLocator(ContainerBuilder $container, string $id, array $mapping, string $class): void
    {
        $serviceLocator = $this->setupServiceLocator($container, $id, $mapping, false);

        $locator = new Definition($class, [
            $serviceLocator,
        ]);

        $container->setDefinition(sprintf('opendxp_ecommerce.locator.%s', $id), $locator);
    }

    private function setupServiceLocator(ContainerBuilder $container, string $id, array $mapping, bool $register = true): Definition
    {
        foreach ($mapping as $name => $reference) {
            $mapping[$name] = new Reference($reference);
        }

        $serviceLocator = new Definition(ServiceLocator::class, [$mapping]);
        $serviceLocator->setPublic(false);
        $serviceLocator->addTag('container.service_locator');

        if ($register) {
            $container->setDefinition(sprintf('opendxp_ecommerce.locator.%s', $id), $serviceLocator);
        }

        return $serviceLocator;
    }

    public function prepend(ContainerBuilder $container): void
    {
        $builds = [
            'opendxpEcommerceFramework' => realpath(__DIR__ . '/../Resources/public/build/ecommerceFramework'),
        ];

        $container->prependExtensionConfig('webpack_encore', [
            //'output_path' => realpath(__DIR__ . '/../Resources/public/build')
            'output_path' => false,
            'builds' => $builds,
        ]);
    }
}
