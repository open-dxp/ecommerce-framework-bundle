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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem;

use OpenDxp\Bundle\EcommerceFrameworkBundle\EnvironmentInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Exception\UnsupportedException;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractSetProductEntry;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\CheckoutableInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\Currency;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem\TaxManagement\TaxCalculationService;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem\TaxManagement\TaxEntry;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\PricingManagerLocatorInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Type\Decimal;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AttributePriceSystem extends CachingPriceSystem implements PriceSystemInterface
{
    protected string $attributeName;

    protected string $priceType;

    protected string $priceClass;

    public function __construct(
        PricingManagerLocatorInterface $pricingManagers,
        protected EnvironmentInterface $environment,
        array $options = []
    ) {
        parent::__construct($pricingManagers);

        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);

        $this->processOptions($resolver->resolve($options));
    }

    protected function processOptions(array $options): void
    {
        $this->attributeName = $options['attribute_name'];
        $this->priceClass = $options['price_class'];
        $this->priceType = $options['price_type'];
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired([
            'attribute_name',
            'price_class',
        ]);

        $resolver->setDefaults([
            'attribute_name' => 'price',
            'price_class' => Price::class,
            'price_type' => TaxCalculationService::CALCULATION_FROM_GROSS,
        ]);

        $resolver->setAllowedTypes('attribute_name', 'string');
        $resolver->setAllowedTypes('price_class', 'string');
        $resolver->setAllowedTypes('price_type', 'string');
    }

    public function createPriceInfoInstance(int|string|null $quantityScale, CheckoutableInterface $product, array $products): AbstractPriceInfo
    {
        $taxClass = $this->getTaxClassForProduct($product);

        $amount = $this->calculateAmount($product, $products);
        $price = $this->getPriceClassInstance($amount);
        $totalPrice = $this->getPriceClassInstance($amount->mul($quantityScale));

        $price->setTaxEntryCombinationMode($taxClass->getTaxEntryCombinationType());
        $price->setTaxEntries(TaxEntry::convertTaxEntries($taxClass));

        $totalPrice->setTaxEntryCombinationMode($taxClass->getTaxEntryCombinationType());
        $totalPrice->setTaxEntries(TaxEntry::convertTaxEntries($taxClass));

        $taxCalculationService = $this->getTaxCalculationService();
        $taxCalculationService->updateTaxes($price, $this->priceType);
        $taxCalculationService->updateTaxes($totalPrice, $this->priceType);

        return new AttributePriceInfo($price, $quantityScale, $totalPrice);
    }

    public function filterProductIds(array $productIds, ?float $fromPrice, ?float $toPrice, string $order, int $offset, int $limit): array
    {
        throw new UnsupportedException(__METHOD__  . ' is not supported for ' . static::class);
    }

    /**
     * Calculates prices from product
     *
     * @param CheckoutableInterface[] $products
     */
    protected function calculateAmount(CheckoutableInterface $product, array $products): Decimal
    {
        $getter = 'get' . ucfirst($this->attributeName);

        if (is_callable([$product, $getter])) {
            if ($products !== []) {
                // TODO where to start using price value object?
                $sum = 0;
                foreach ($products as $p) {
                    if ($p instanceof AbstractSetProductEntry) {
                        $sum += $p->getProduct()->$getter() * $p->getQuantity();
                    } else {
                        $sum += $p->$getter();
                    }
                }

                return Decimal::create($sum);
            }

            return Decimal::create((float) $product->$getter());
        }

        return Decimal::zero();
    }

    /**
     * Returns default currency based on environment settings
     */
    protected function getDefaultCurrency(): Currency
    {
        return $this->environment->getDefaultCurrency();
    }

    /**
     * Creates instance of PriceInterface
     */
    protected function getPriceClassInstance(Decimal $amount): PriceInterface
    {
        $priceClass = $this->priceClass;

        return new $priceClass($amount, $this->getDefaultCurrency(), false);
    }
}
