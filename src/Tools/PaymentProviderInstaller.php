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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\Tools;

use Exception;
use OpenDxp\Extension\Bundle\Installer\AbstractInstaller;
use OpenDxp\Model\DataObject\ClassDefinition;
use OpenDxp\Model\DataObject\ClassDefinition\Service;
use OpenDxp\Model\DataObject\Objectbrick;

class PaymentProviderInstaller extends AbstractInstaller
{
    /**
     * @var string // json source path
     */
    protected string $bricksPath;

    /**
     * @var array<string, string> //$brickKey => $brickImportJsonPath
     */
    protected array $bricksToInstall = [];

    public function canBeInstalled(): bool
    {
        return ClassDefinition::getByName('OnlineShopOrder') && !$this->isInstalled();
    }

    public function canBeUninstalled(): bool
    {
        return $this->isInstalled();
    }

    public function install(): void
    {
        $this->installBricks();
    }

    public function uninstall(): void
    {
        $this->unInstallBricks();
    }

    public function isInstalled(): bool
    {
        $installed = false;

        try {
            // check if payment brick exists
            foreach ($this->bricksToInstall as $brickKey => $brickFile) {
                $installed = Objectbrick\Definition::getByKey($brickKey);
            }
        } catch (Exception $e) {
            // nothing to do
        }

        return (bool) $installed;
    }

    public function needsReloadAfterInstall(): bool
    {
        return true;
    }

    protected function installBricks(): void
    {
        foreach ($this->bricksToInstall as $brickKey => $brickFile) {
            self::installBrick($brickKey, $this->bricksPath . $brickFile);
        }
    }

    protected function unInstallBricks(): void
    {
        foreach ($this->bricksToInstall as $brickKey => $brickFile) {
            $brick = Objectbrick\Definition::getByKey($brickKey);
            if ($brick instanceof Objectbrick\Definition) {
                $brick->delete();
            }
        }
    }

    protected static function installBrick(string $brickKey, string $filepath): void
    {
        try {
            $brick = Objectbrick\Definition::getByKey($brickKey);
        } catch (Exception $e) {
            $brick = null;
        }

        if (!$brick) {
            $brick = new Objectbrick\Definition;
            $brick->setKey($brickKey);

            $json = file_get_contents($filepath);

            $success = Service::importObjectBrickFromJson($brick, $json);

            if ($success) {
                $onlineOrderClass = ClassDefinition::getByName('OnlineShopOrder');
                /** @var ClassDefinition\Data\Objectbricks $paymentProviderBrickField */
                $paymentProviderBrickField = $onlineOrderClass->getFieldDefinition('paymentProvider');
                $allowedTypes = $paymentProviderBrickField->getAllowedTypes();
                $paymentProviderBrickField->setAllowedTypes([$brickKey, ...$allowedTypes]);
            }
        }
    }
}
