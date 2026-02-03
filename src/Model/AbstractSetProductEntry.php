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

/**
 * Class for product entry of a set product - container for product and quantity
 */
class AbstractSetProductEntry
{
    private int $quantity;

    private CheckoutableInterface $product;

    public function __construct(CheckoutableInterface $product, int $quantity = 1)
    {
        $this->product = $product;
        $this->quantity = $quantity;
    }

    public function setProduct(CheckoutableInterface $product): void
    {
        $this->product = $product;
    }

    public function getProduct(): CheckoutableInterface
    {
        return $this->product;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * returns id of set product
     */
    public function getId(): int
    {
        return $this->getProduct()->getId();
    }
}
