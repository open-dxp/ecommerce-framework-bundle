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

/**
 * Base implementation for a lazy loading price info
 */
class LazyLoadingPriceInfo extends AbstractPriceInfo implements PriceInfoInterface
{
    /**
     * @var PriceInfoInterface[]
     */
    protected array $priceRegistry = [];

    #[\Override]
    public static function getInstance(): static
    {
        return parent::getInstance();
    }

    public function __call(string $name, array $arg): mixed
    {
        if (array_key_exists($name, $this->priceRegistry)) {
            return $this->priceRegistry[$name];
        }
        if (method_exists($this, '_' . $name)) {
            $priceInfo = $this->{'_' . $name}();
        } elseif (method_exists($this->getPriceSystem(), $name)) {
            $method = $name;
            $priceInfo = $this->getPriceSystem()->$method($this->getProduct(), $this->getQuantity(), $this->getProducts());
        } else {
            throw new \OpenDxp\Bundle\EcommerceFrameworkBundle\Exception\UnsupportedException($name . ' is not supported for ' . static::class);
        }
        if ($priceInfo != null && method_exists($priceInfo, 'setPriceSystem')) {
            $priceInfo->setPriceSystem($this->getPriceSystem());
        }
        $this->priceRegistry[$name] = $priceInfo;

        return $this->priceRegistry[$name];
    }
}
