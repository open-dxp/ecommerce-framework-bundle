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

use OpenDxp\Bundle\EcommerceFrameworkBundle\Exception\UnsupportedException;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\CheckoutableInterface;
use Override;

/**
 * Price system which caches created price info objects per product and request
 */
abstract class CachingPriceSystem extends AbstractPriceSystem implements CachingPriceSystemInterface
{
    /**
     * @var PriceInfoInterface[][] $priceInfos
     */
    protected array $priceInfos = [];

    #[Override]
    public function getPriceInfo(CheckoutableInterface $product, int|string|null $quantityScale = null, ?array $products = null): PriceInfoInterface
    {
        $pId = $product->getId();
        if (!is_array($this->priceInfos[$pId] ?? null)) {
            $this->priceInfos[$pId] = [];
        }

        $quantityScaleKey = (string) $quantityScale;

        if (empty($this->priceInfos[$pId][$quantityScaleKey])) {
            $priceInfo = $this->initPriceInfoInstance($quantityScale, $product, $products ?? []);
            $this->priceInfos[$pId][$quantityScaleKey] = $priceInfo;
        }

        return $this->priceInfos[$pId][$quantityScaleKey];
    }

    public function loadPriceInfos(array $productEntries, array $options): mixed
    {
        throw new UnsupportedException(__METHOD__  . ' is not supported for ' . static::class);
    }

    public function clearPriceInfos(array $productEntries, array $options): mixed
    {
        throw new UnsupportedException(__METHOD__  . ' is not supported for ' . static::class);
    }

    public function filterProductIds(array $productIds, ?float $fromPrice, ?float $toPrice, string $order, int $offset, int $limit): array
    {
        throw new UnsupportedException(__METHOD__  . ' is not supported for ' . static::class);
    }
}
