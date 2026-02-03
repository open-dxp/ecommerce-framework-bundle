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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle;

use OpenDxp;
use OpenDxp\Bundle\EcommerceFrameworkBundle\AvailabilitySystem\AvailabilitySystemInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\AvailabilitySystem\AvailabilitySystemLocatorInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartManagerInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartManagerLocatorInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\CheckoutManager\CheckoutManagerFactoryLocatorInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\CheckoutManager\CommitOrderProcessorInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\CheckoutManager\CommitOrderProcessorLocatorInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\CheckoutManager\V7\CheckoutManagerInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\DependencyInjection\OpenDxpEcommerceFrameworkExtension;
use OpenDxp\Bundle\EcommerceFrameworkBundle\FilterService\FilterService;
use OpenDxp\Bundle\EcommerceFrameworkBundle\FilterService\FilterServiceLocatorInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\IndexService\IndexService;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractVoucherTokenType;
use OpenDxp\Bundle\EcommerceFrameworkBundle\OfferTool\ServiceInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\OrderManager\OrderManagerLocatorInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\OrderManager\V7\OrderManagerInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PaymentManager\PaymentManagerInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem\PriceSystemInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem\PriceSystemLocatorInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\PricingManagerInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\PricingManagerLocatorInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Tracking\TrackingManagerInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\VoucherService\TokenManager\TokenManagerInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\VoucherService\VoucherServiceInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Factory
{
    /**
     * Systems with multiple instances (e.g. price systems or tenant specific systems) are
     * injected through a service locator which is indexed by tenant/name. All other services
     * are loaded from the container on demand to make sure only services needed are built.
     */
    public function __construct(
        private readonly ContainerInterface $container,
        /**
         * Tenant specific cart managers
         */
        private readonly CartManagerLocatorInterface $cartManagers,
        /**
         * Tenant specific order managers
         */
        private readonly OrderManagerLocatorInterface $orderManagers,
        /**
         * Pricing managers registered by tenant
         */
        private readonly PricingManagerLocatorInterface $pricingManagers,
        /**
         * Price systems registered by name
         */
        private readonly PriceSystemLocatorInterface $priceSystems,
        /**
         * Availability systems registered by name
         */
        private readonly AvailabilitySystemLocatorInterface $availabilitySystems,
        /**
         * Checkout manager factories registered by tenant
         */
        private readonly CheckoutManagerFactoryLocatorInterface $checkoutManagerFactories,
        /**
         * Commit order processors registered by tenant
         */
        private readonly CommitOrderProcessorLocatorInterface $commitOrderProcessors,
        /**
         * Filter services registered by ^tenant
         */
        private readonly FilterServiceLocatorInterface $filterServices
    )
    {
    }

    public static function getInstance(): self
    {
        return OpenDxp::getContainer()->get(OpenDxpEcommerceFrameworkExtension::SERVICE_ID_FACTORY);
    }

    public function getEnvironment(): EnvironmentInterface
    {
        return $this->container->get(OpenDxpEcommerceFrameworkExtension::SERVICE_ID_ENVIRONMENT);
    }

    /**
     * Returns cart manager for a specific tenant. If no tenant is passed it will fall back to the current
     * checkout tenant or to "default" if no current checkout tenant is set.
     */
    public function getCartManager(string $tenant = null): CartManagerInterface
    {
        return $this->cartManagers->getCartManager($tenant);
    }

    /**
     * Returns order manager for a specific tenant. If no tenant is passed it will fall back to the current
     * checkout tenant or to "default" if no current checkout tenant is set.
     */
    public function getOrderManager(string $tenant = null): OrderManagerInterface
    {
        return $this->orderManagers->getOrderManager($tenant);
    }

    /**
     * Returns pricing manager for a specific tenant. If no tenant is passed it will fall back to the current
     * checkout tenant or to "default" if no current checkout tenant is set.
     */
    public function getPricingManager(string $tenant = null): PricingManagerInterface
    {
        return $this->pricingManagers->getPricingManager($tenant);
    }

    /**
     * Returns a price system by name. Falls back to "default" if no name is passed.
     */
    public function getPriceSystem(string $name = null): PriceSystemInterface
    {
        return $this->priceSystems->getPriceSystem($name);
    }

    /**
     * Returns an availability system by name. Falls back to "default" if no name is passed.
     */
    public function getAvailabilitySystem(string $name = null): AvailabilitySystemInterface
    {
        return $this->availabilitySystems->getAvailabilitySystem($name);
    }

    /**
     * Returns checkout manager for a specific tenant. If no tenant is passed it will fall back to the current
     * checkout tenant or to "default" if no current checkout tenant is set.
     */
    public function getCheckoutManager(CartInterface $cart, string $tenant = null): CheckoutManagerInterface
    {
        $factory = $this->checkoutManagerFactories->getCheckoutManagerFactory($tenant);

        return $factory->createCheckoutManager($cart);
    }

    /**
     * Returns a commit order processor which is configured for a specific checkout manager
     */
    public function getCommitOrderProcessor(string $tenant = null): CommitOrderProcessorInterface
    {
        return $this->commitOrderProcessors->getCommitOrderProcessor($tenant);
    }

    public function getPaymentManager(): PaymentManagerInterface
    {
        return $this->container->get(OpenDxpEcommerceFrameworkExtension::SERVICE_ID_PAYMENT_MANAGER);
    }

    /**
     * Returns the index service which holds a collection of all index workers
     */
    public function getIndexService(): IndexService
    {
        return $this->container->get(OpenDxpEcommerceFrameworkExtension::SERVICE_ID_INDEX_SERVICE);
    }

    /**
     * Returns the filter service for the currently set assortment tenant. Falls back to "default" if no tenant is passed
     * and there is no current assortment tenant set.
     */
    public function getFilterService(string $tenant = null): FilterService
    {
        return $this->filterServices->getFilterService($tenant);
    }

    public function getAllTenants(): array
    {
        return $this->getIndexService()->getTenants();
    }

    public function getOfferToolService(): ServiceInterface
    {
        return $this->container->get(OpenDxpEcommerceFrameworkExtension::SERVICE_ID_OFFER_TOOL);
    }

    public function getVoucherService(): VoucherServiceInterface
    {
        return $this->container->get(OpenDxpEcommerceFrameworkExtension::SERVICE_ID_VOUCHER_SERVICE);
    }

    /**
     * Builds a token manager for a specific token configuration
     */
    public function getTokenManager(AbstractVoucherTokenType $configuration): TokenManagerInterface
    {
        $tokenManagerFactory = $this->container->get(OpenDxpEcommerceFrameworkExtension::SERVICE_ID_TOKEN_MANAGER_FACTORY);

        return $tokenManagerFactory->getTokenManager($configuration);
    }

    public function getTrackingManager(): TrackingManagerInterface
    {
        return $this->container->get(OpenDxpEcommerceFrameworkExtension::SERVICE_ID_TRACKING_MANAGER);
    }

    public function saveState(): void
    {
        $this->getCartManager()->save();
        $this->getEnvironment()->save();
    }
}
