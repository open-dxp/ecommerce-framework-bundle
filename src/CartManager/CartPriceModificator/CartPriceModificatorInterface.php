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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartPriceModificator;

use OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem\ModificatedPriceInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem\PriceInterface;

interface CartPriceModificatorInterface
{
    public function getName(): string;

    /**
     * function which modifies the current sub total price
     *
     * @param PriceInterface $currentSubTotal - current sub total which is modified and returned
     * @param CartInterface $cart - cart
     */
    public function modify(PriceInterface $currentSubTotal, CartInterface $cart): ?ModificatedPriceInterface;
}
