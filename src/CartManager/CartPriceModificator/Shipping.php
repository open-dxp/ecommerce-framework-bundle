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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartPriceModificator;

use OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Factory;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem\ModificatedPrice;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem\ModificatedPriceInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem\PriceInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem\TaxManagement\TaxEntry;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Type\Decimal;
use OpenDxp\Model\DataObject\OnlineShopTaxClass;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Shipping implements ShippingInterface
{
    protected Decimal $charge;

    protected ?OnlineShopTaxClass $taxClass = null;

    public function __construct(array $options = [])
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);

        $this->processOptions($resolver->resolve($options));
    }

    protected function processOptions(array $options): void
    {
        $this->charge = Decimal::create($options['charge']);
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'charge' => 0,
        ]);
    }

    public function getName(): string
    {
        return 'shipping';
    }

    public function modify(PriceInterface $currentSubTotal, CartInterface $cart): ModificatedPrice|ModificatedPriceInterface
    {
        $modificatedPrice = new ModificatedPrice($this->getCharge(), $currentSubTotal->getCurrency());

        $taxClass = $this->getTaxClass();
        if ($taxClass) {
            $modificatedPrice->setTaxEntryCombinationMode($taxClass->getTaxEntryCombinationType());
            $modificatedPrice->setTaxEntries(TaxEntry::convertTaxEntries($taxClass));

            $modificatedPrice->setGrossAmount($this->getCharge(), true);
        }

        return $modificatedPrice;
    }

    public function setCharge(Decimal $charge): CartPriceModificatorInterface
    {
        $this->charge = $charge;

        return $this;
    }

    public function getCharge(): Decimal
    {
        return $this->charge;
    }

    public function getTaxClass(): ?OnlineShopTaxClass
    {
        if (empty($this->taxClass)) {
            $this->taxClass = Factory::getInstance()->getPriceSystem('default')->getTaxClassForPriceModification($this);
        }

        return $this->taxClass;
    }

    public function setTaxClass(OnlineShopTaxClass $taxClass): void
    {
        $this->taxClass = $taxClass;
    }
}
