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
 * Attribute info for attribute price system
 */
class AttributePriceInfo extends AbstractPriceInfo implements PriceInfoInterface
{
    public function __construct(
        protected PriceInterface $price,
        int $quantity,
        protected PriceInterface $totalPrice
    ) {
        $this->quantity = $quantity;
    }

    public function getPrice(): PriceInterface
    {
        return $this->price;
    }

    public function getTotalPrice(): PriceInterface
    {
        return $this->totalPrice;
    }

    /**
     * Try to delegate all other functions to the product
     */
    public function __call(string $name, array $arguments): mixed
    {
        return $this->product->$name($arguments);
    }
}
