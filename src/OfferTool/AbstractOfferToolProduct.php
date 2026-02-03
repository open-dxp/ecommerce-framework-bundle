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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\OfferTool;

use OpenDxp\Bundle\EcommerceFrameworkBundle\AvailabilitySystem\AvailabilityInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\AvailabilitySystem\AvailabilitySystemInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Exception\UnsupportedException;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Factory;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\CheckoutableInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem\PriceInfoInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem\PriceInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem\PriceSystemInterface;
use OpenDxp\Model\DataObject;

/**
 * Abstract base class for pimcore objects who should be used as custom products in the offer tool
 */
abstract class AbstractOfferToolProduct extends \OpenDxp\Model\DataObject\Concrete implements CheckoutableInterface
{
    // =============================================
    //     CheckoutableInterface Methods
    //  =============================================

    /**
     * should be overwritten in mapped sub classes of product classes
     */
    abstract public function getOSName(): ?string;

    /**
     * should be overwritten in mapped sub classes of product classes
     */
    abstract public function getOSProductNumber(): ?string;

    /**
     * defines the name of the availability system for this product.
     * for offline tool there are no availability systems implemented
     */
    public function getAvailabilitySystemName(): string
    {
        return 'none';
    }

    /**
     * checks if product is bookable
     * returns always true in default implementation
     */
    public function getOSIsBookable(int $quantityScale = 1): bool
    {
        return true;
    }

    /**
     * defines the name of the price system for this product.
     * there should either be a attribute in pro product object or
     * it should be overwritten in mapped sub classes of product classes
     */
    public function getPriceSystemName(): string
    {
        return 'defaultOfferToolPriceSystem';
    }

    /**
     * returns instance of price system implementation based on result of getPriceSystemName()
     */
    public function getPriceSystemImplementation(): PriceSystemInterface
    {
        return Factory::getInstance()->getPriceSystem($this->getPriceSystemName());
    }

    /**
     * returns instance of availability system implementation based on result of getAvailabilitySystemName()
     */
    public function getAvailabilitySystemImplementation(): AvailabilitySystemInterface
    {
        return Factory::getInstance()->getAvailabilitySystem($this->getAvailabilitySystemName());
    }

    /**
     * returns price for given quantity scale
     */
    public function getOSPrice(int $quantityScale = 1): PriceInterface
    {
        return $this->getOSPriceInfo($quantityScale)->getPrice();
    }

    /**
     * returns price info for given quantity scale.
     * price info might contain price and additional information for prices like discounts, ...
     */
    public function getOSPriceInfo(int $quantityScale = 1): PriceInfoInterface
    {
        return $this->getPriceSystemImplementation()->getPriceInfo($this, $quantityScale);
    }

    /**
     * returns availability info based on given quantity
     */
    public function getOSAvailabilityInfo(?int $quantity = null): AvailabilityInterface
    {
        return $this->getAvailabilitySystemImplementation()->getAvailabilityInfo($this, $quantity);
    }

    #[\Override]
    public static function getById(int|string $id, array $params = []): ?static
    {
        if (is_string($id)) {
            trigger_deprecation(
                'open-dxp/opendxp',
                '1.0',
                sprintf('Passing id as string to method %s is deprecated', __METHOD__)
            );
            $id = is_numeric($id) ? (int) $id : 0;
        }
        $object = DataObject::getById($id, $params);

        if ($object instanceof AbstractOfferToolProduct) {
            return $object;
        }

        return null;
    }

    /**
     * @throws UnsupportedException
     */
    public function getProductGroup(): ?string
    {
        throw new UnsupportedException('getProductGroup is not implemented for ' . static::class);
    }
}
