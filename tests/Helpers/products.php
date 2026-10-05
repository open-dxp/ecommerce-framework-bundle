<?php

declare(strict_types=1);

use OpenDxp\Bundle\EcommerceFrameworkBundle\Environment;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractCategory;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractProduct;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\CheckoutableInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\Currency;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem\AttributePriceSystem;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem\Price;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem\PriceInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem\PriceSystemInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem\TaxManagement\TaxEntry;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\PricingManager;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\PricingManagerInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\PricingManagerLocatorInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Type\Decimal;
use OpenDxp\Localization\LocaleService;
use OpenDxp\Model\DataObject\Fieldcollection;
use OpenDxp\Model\DataObject\Fieldcollection\Data\TaxEntry as TaxEntryData;
use OpenDxp\Model\DataObject\OnlineShopTaxClass;

/**
 * A product that is never saved. Its price system answers with the given gross price in euros and applies the rules of
 * the pricing manager.
 *
 * @param array<string, float|int> $taxes percent by tax name
 * @param list<AbstractCategory> $categories
 */
function product(
    float|int|string $grossPrice,
    ?PricingManagerInterface $pricing = null,
    array $taxes = [],
    string $calculationMode = TaxEntry::CALCULATION_MODE_COMBINE,
    ?int $id = null,
    array $categories = [],
    ?AbstractProduct $parent = null,
): AbstractProduct {
    $priceSystem = priceSystem(Decimal::create($grossPrice), taxClass($taxes, $calculationMode), $pricing ?? new PricingManager([], []));

    return new class($id ?? random_int(1, PHP_INT_MAX), $priceSystem, $categories, $parent) extends AbstractProduct {
        /**
         * @param list<AbstractCategory> $productCategories
         */
        public function __construct(
            private readonly int $productId,
            private readonly PriceSystemInterface $priceSystem,
            private readonly array $productCategories,
            private readonly ?AbstractProduct $parentProduct,
        ) {
        }

        public function getId(): int
        {
            return $this->productId;
        }

        public function getPriceSystemImplementation(): PriceSystemInterface
        {
            return $this->priceSystem;
        }

        public function getCategories(): array
        {
            return $this->productCategories;
        }

        public function getParent(): ?AbstractProduct
        {
            return $this->parentProduct;
        }
    };
}

/**
 * A category that is never saved.
 */
function category(string $path): AbstractCategory
{
    return new class($path) extends AbstractCategory {
        public function __construct(private readonly string $categoryPath)
        {
        }

        public function getFullPath(): string
        {
            return $this->categoryPath;
        }
    };
}

/**
 * @param array<string, float|int> $taxes percent by tax name
 */
function taxClass(array $taxes = [], string $calculationMode = TaxEntry::CALCULATION_MODE_COMBINE): OnlineShopTaxClass
{
    $entries = new Fieldcollection();

    foreach ($taxes as $name => $percent) {
        $entry = new TaxEntryData();
        $entry->setName((string) $name);
        $entry->setPercent($percent);
        $entries->add($entry);
    }

    $taxClass = new OnlineShopTaxClass();
    $taxClass->setTaxEntries($entries);
    $taxClass->setTaxEntryCombinationType($calculationMode);

    return $taxClass;
}

function priceSystem(Decimal $grossPrice, OnlineShopTaxClass $taxClass, PricingManagerInterface $pricing): AttributePriceSystem
{
    $pricingManagers = new readonly class($pricing) implements PricingManagerLocatorInterface {
        public function __construct(private PricingManagerInterface $pricing)
        {
        }

        public function getPricingManager(?string $tenant = null): PricingManagerInterface
        {
            return $this->pricing;
        }

        public function hasPricingManager(string $tenant): bool
        {
            return true;
        }
    };

    return new class($pricingManagers, $grossPrice, $taxClass) extends AttributePriceSystem {
        public function __construct(PricingManagerLocatorInterface $pricingManagers, private readonly Decimal $grossPrice, private readonly OnlineShopTaxClass $taxClass)
        {
            parent::__construct($pricingManagers, new Environment(new LocaleService()));
        }

        public function getTaxClassForProduct(CheckoutableInterface $product): OnlineShopTaxClass
        {
            return $this->taxClass;
        }

        protected function calculateAmount(CheckoutableInterface $product, array $products): Decimal
        {
            return $this->grossPrice;
        }

        protected function getPriceClassInstance(Decimal $amount): PriceInterface
        {
            return new Price($amount, new Currency('EUR'));
        }
    };
}
