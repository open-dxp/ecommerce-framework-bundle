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

use OpenDxp\Bundle\ApplicationLoggerBundle\OpenDxpApplicationLoggerBundle;
use OpenDxp\Bundle\EcommerceFrameworkBundle\DependencyInjection\Compiler\RegisterConfiguredServicesPass;
use OpenDxp\Bundle\EcommerceFrameworkBundle\DependencyInjection\OpenDxpEcommerceFrameworkExtension;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Tools\Installer;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Type\Decimal;
use OpenDxp\Bundle\GoogleMarketingBundle\OpenDxpGoogleMarketingBundle;
use OpenDxp\Bundle\PersonalizationBundle\OpenDxpPersonalizationBundle;
use OpenDxp\Extension\Bundle\AbstractOpenDxpBundle;
use OpenDxp\Extension\Bundle\OpenDxpBundleAdminClassicInterface;
use OpenDxp\Extension\Bundle\Traits\BundleAdminClassicTrait;
use OpenDxp\HttpKernel\Bundle\DependentBundleInterface;
use OpenDxp\HttpKernel\BundleCollection\BundleCollection;
use OpenDxp\Version;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\WebpackEncoreBundle\WebpackEncoreBundle;

class OpenDxpEcommerceFrameworkBundle extends AbstractOpenDxpBundle implements DependentBundleInterface, OpenDxpBundleAdminClassicInterface
{
    use BundleAdminClassicTrait;

    #[\Override]
    public function getContainerExtension(): ?ExtensionInterface
    {
        if ($this->extension === null) {
            $this->extension = new OpenDxpEcommerceFrameworkExtension();
        }

        return $this->extension;
    }

    #[\Override]
    public function getVersion(): string
    {
        return sprintf('%s build %s', Version::getVersion(), Version::getRevision());
    }

    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new RegisterConfiguredServicesPass());
    }

    public function getCssPaths(): array
    {
        return [
            '/bundles/opendxpecommerceframework/css/backend.css',
            '/bundles/opendxpecommerceframework/css/pricing.css',
        ];
    }

    public function getJsPaths(): array
    {
        return [
            '/bundles/opendxpecommerceframework/js/indexfieldselectionfield/data/indexFieldSelectionField.js',
            '/bundles/opendxpecommerceframework/js/indexfieldselectionfield/tags/indexFieldSelectionField.js',
            '/bundles/opendxpecommerceframework/js/indexfieldselectionfield/data/indexFieldSelectionCombo.js',
            '/bundles/opendxpecommerceframework/js/indexfieldselectionfield/tags/indexFieldSelectionCombo.js',
            '/bundles/opendxpecommerceframework/js/indexfieldselectionfield/data/indexFieldSelection.js',
            '/bundles/opendxpecommerceframework/js/indexfieldselectionfield/tags/indexFieldSelection.js',
            '/bundles/opendxpecommerceframework/js/startup.js',
            '/bundles/opendxpecommerceframework/js/pricing/config/panel.js',
            '/bundles/opendxpecommerceframework/js/pricing/config/item.js',
            '/bundles/opendxpecommerceframework/js/pricing/config/objects.js',
            '/bundles/opendxpecommerceframework/js/pricing/conditions/targetGroup.js',
            '/bundles/opendxpecommerceframework/js/voucherservice/VoucherSeriesTab.js',
            '/bundles/opendxpecommerceframework/js/order/OrderTab.js',
            '/admin/ecommerceframework/config/js-config',
        ];
    }

    public function boot(): void
    {
        $container = $this->container;
        // set default decimal scale from config
        Decimal::setDefaultScale($container->getParameter('opendxp_ecommerce.decimal_scale'));
    }

    public function getInstaller(): Installer
    {
        return $this->container->get(Installer::class);
    }

    public static function registerDependentBundles(BundleCollection $collection): void
    {
        $collection->addBundle(OpenDxpApplicationLoggerBundle::class);
        $collection->addBundle(OpenDxpPersonalizationBundle::class);
        $collection->addBundle(OpenDxpGoogleMarketingBundle::class);
        $collection->addBundle(new WebpackEncoreBundle());
    }
}
