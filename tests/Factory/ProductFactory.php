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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\Tests\Factory;

use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractProduct;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem\TaxManagement\TaxEntry;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\PricingManager;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\PricingManagerInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Tests\Application\Model\MockProduct;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Tests\Application\PriceSystem\MockPriceSystem;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Tests\Application\PricingManager\MockPricingManagerLocator;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Type\Decimal;
use Zenstruck\Foundry\ObjectFactory;

/**
 * @extends ObjectFactory<MockProduct>
 */
final class ProductFactory extends ObjectFactory
{
    public static function class(): string
    {
        return MockProduct::class;
    }

    public function costing(float|int|string $grossPrice): static
    {
        return $this->with(['grossPrice' => $grossPrice]);
    }

    /**
     * @param array<string, float|int> $taxes percent by tax name
     */
    public function taxedBy(array $taxes, string $calculationMode): static
    {
        return $this->with([
            'taxes' => $taxes,
            'calculationMode' => $calculationMode,
        ]);
    }

    public function pricedBy(PricingManagerInterface $pricing): static
    {
        return $this->with(['pricing' => $pricing]);
    }

    public function withId(int $id): static
    {
        return $this->with(['id' => $id]);
    }

    public function variantOf(AbstractProduct $parent): static
    {
        return $this->with(['parent' => $parent]);
    }

    protected function defaults(): array
    {
        return [
            'id' => self::faker()->unique()->numberBetween(1000, 999_999),
            'grossPrice' => self::faker()->numberBetween(1, 500),
            'taxes' => [],
            'calculationMode' => TaxEntry::CALCULATION_MODE_COMBINE,
            'pricing' => new PricingManager([], []),
            'parent' => null,
        ];
    }

    protected function initialize(): static
    {
        return $this->instantiateWith(static fn (array $attributes): MockProduct => new MockProduct(
            $attributes['id'],
            new MockPriceSystem(
                new MockPricingManagerLocator($attributes['pricing']),
                Decimal::create($attributes['grossPrice']),
                taxClass($attributes['taxes'], $attributes['calculationMode']),
            ),
            [],
            $attributes['parent'],
        ));
    }
}
