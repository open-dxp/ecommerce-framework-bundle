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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\Tests\Support\Helper;

use Codeception\Lib\ModuleContainer;
use Codeception\Module;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\Condition\VoucherToken;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Tools\Installer;
use OpenDxp\Model\DataObject\FilterDefinition;
use OpenDxp\Model\DataObject\OfferToolCustomProduct;
use OpenDxp\Model\DataObject\OfferToolOffer;
use OpenDxp\Model\DataObject\OfferToolOfferItem;
use OpenDxp\Model\DataObject\OnlineShopOrder;
use OpenDxp\Model\DataObject\OnlineShopOrderItem;
use OpenDxp\Model\DataObject\OnlineShopTaxClass;
use OpenDxp\Model\DataObject\OnlineShopVoucherSeries;
use OpenDxp\Tests\Support\Helper\OpenDxp;
use OpenDxp\Tests\Support\Util\Autoloader;

class Ecommerce extends Module
{
    public function __construct(ModuleContainer $moduleContainer, ?array $config = null)
    {
        $this->config = array_merge($this->config, [
            'run_installer' => true,
        ]);

        parent::__construct($moduleContainer, $config);
    }

    public function _beforeSuite(array $settings = []): void
    {
        if ($this->config['run_installer']) {
            /** @var OpenDxp $opendxpModule */
            $opendxpModule = $this->getModule('\\' . OpenDxp::class);

            $this->debug('[ECOMMERCE] Running ecommerce framework installer');

            // install ecommerce framework
            $installer = $opendxpModule->getContainer()->get(Installer::class);
            $installer->install();

            //explicitly load installed classes so that the new ones are used during tests
            Autoloader::load(OnlineShopTaxClass::class);
            Autoloader::load(FilterDefinition::class);
            Autoloader::load(OfferToolCustomProduct::class);
            Autoloader::load(OfferToolOfferItem::class);
            Autoloader::load(OfferToolOffer::class);
            Autoloader::load(OnlineShopOrderItem::class);
            Autoloader::load(OnlineShopOrder::class);
            Autoloader::load(OnlineShopVoucherSeries::class);
            Autoloader::load(VoucherToken::class);
        }
    }
}
