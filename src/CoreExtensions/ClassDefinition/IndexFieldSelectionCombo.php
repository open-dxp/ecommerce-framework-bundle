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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\CoreExtensions\ClassDefinition;

use Exception;
use OpenDxp;
use OpenDxp\Bundle\EcommerceFrameworkBundle\DependencyInjection\OpenDxpEcommerceFrameworkExtension;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Factory;
use OpenDxp\Bundle\EcommerceFrameworkBundle\IndexService\ProductList\ProductListInterface;
use OpenDxp\Logger;
use OpenDxp\Model\DataObject\ClassDefinition\Data\Select;
use OpenDxp\Model\DataObject\ClassDefinition\Service;

class IndexFieldSelectionCombo extends Select
{
    /**
     * @deprecated Will be removed in ecommerce-framework-bundle 2, use getFieldType() instead.
     */
    public string $fieldtype = 'indexFieldSelectionCombo';

    public bool $specificPriceField = false;

    public bool $showAllFields = false;

    public bool $considerTenants = false;

    public function __construct()
    {
        $this->setOptions($this->buildOptions());
    }

    protected function buildOptions(): array
    {
        $options = [];

        if (OpenDxp::getContainer()->has(OpenDxpEcommerceFrameworkExtension::SERVICE_ID_FACTORY)) {
            try {
                $indexService = Factory::getInstance()->getIndexService();
                $indexColumns = $indexService->getIndexAttributes(true);

                foreach ($indexColumns as $c) {
                    $options[] = [
                        'key' => $c,
                        'value' => $c,
                    ];
                }

                if ($this->getSpecificPriceField()) {
                    $options[] = [
                        'key' => ProductListInterface::ORDERKEY_PRICE,
                        'value' => ProductListInterface::ORDERKEY_PRICE,
                    ];
                }
            } catch (Exception $e) {
                Logger::error((string) $e);
            }
        }

        return $options;
    }

    public function setSpecificPriceField(bool $specificPriceField): void
    {
        $this->specificPriceField = $specificPriceField;
    }

    public function getSpecificPriceField(): bool
    {
        return $this->specificPriceField;
    }

    public function setShowAllFields(bool $showAllFields): void
    {
        $this->showAllFields = $showAllFields;
    }

    public function getShowAllFields(): bool
    {
        return $this->showAllFields;
    }

    public function setConsiderTenants(bool $considerTenants): void
    {
        $this->considerTenants = $considerTenants;
    }

    public function getConsiderTenants(): bool
    {
        return $this->considerTenants;
    }

    #[\Override]
    public function jsonSerialize(): mixed
    {
        if (Service::doRemoveDynamicOptions()) {
            $this->options = null;
        }

        return parent::jsonSerialize();
    }

    #[\Override]
    public function getFieldType(): string
    {
        return 'indexFieldSelectionCombo';
    }
}
