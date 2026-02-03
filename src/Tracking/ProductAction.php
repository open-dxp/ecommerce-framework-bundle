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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\Tracking;

class ProductAction extends AbstractProductData
{
    protected float|int $quantity = 1;

    protected string $coupon = '';

    public function getQuantity(): float|int
    {
        return $this->quantity;
    }

    public function setQuantity(float|int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getCoupon(): string
    {
        return $this->coupon;
    }

    public function setCoupon(string $coupon): static
    {
        $this->coupon = $coupon;

        return $this;
    }
}
