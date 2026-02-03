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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\Model;

use OpenDxp;
use OpenDxp\Bundle\EcommerceFrameworkBundle\AvailabilitySystem\AvailabilityInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\AvailabilitySystem\AvailabilitySystemInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\EnvironmentInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Factory;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem\Price;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem\PriceInfoInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem\PriceInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem\PriceSystemInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Type\Decimal;
use OpenDxp\Model\DataObject\Concrete;

/**
 * Mock Product class which should be used as a product when actual product is not available in the System.
 */
class MockProduct extends Concrete implements ProductInterface, IndexableInterface, CheckoutableInterface
{
    public function getAvailabilitySystemName(): string
    {
        return 'default';
    }

    public function getOSIsBookable(int $quantityScale = 1): bool
    {
        return false;
    }

    public function getPriceSystemImplementation(): PriceSystemInterface
    {
        return Factory::getInstance()->getPriceSystem($this->getPriceSystemName());
    }

    public function getAvailabilitySystemImplementation(): AvailabilitySystemInterface
    {
        return Factory::getInstance()->getAvailabilitySystem($this->getAvailabilitySystemName());
    }

    public function getOSPrice(int $quantityScale = 1): PriceInterface
    {
        /** @var EnvironmentInterface $environment */
        $environment = OpenDxp::getKernel()->getContainer()->get(EnvironmentInterface::class);

        return new Price(Decimal::create(0), $environment->getDefaultCurrency());
    }

    public function getOSPriceInfo(int $quantityScale = 1): PriceInfoInterface
    {
        return $this->getPriceSystemImplementation()->getPriceInfo($this, 0);
    }

    public function getOSAvailabilityInfo(?int $quantity = null): AvailabilityInterface
    {
        return $this->getAvailabilitySystemImplementation()->getAvailabilityInfo($this, 0);
    }

    public function getOSDoIndexProduct(): bool
    {
        return false;
    }

    public function getPriceSystemName(): string
    {
        return 'default';
    }

    public function isActive(bool $inProductList = false): bool
    {
        return false;
    }

    public function getOSIndexType(): ?string
    {
        return null;
    }

    public function getOSParentId(): int|null
    {
        return null;
    }

    public function getCategories(): ?array
    {
        return null;
    }

    public function getOSName(): ?string
    {
        return 'Product Not Available';
    }

    public function getOSProductNumber(): ?string
    {
        return null;
    }

    public function getPrice(): int
    {
        return 0;
    }

    #[\Override]
    public function __call(string $method, array $args): mixed
    {
        return null;
    }
}
