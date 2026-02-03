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

use OpenDxp\Bundle\EcommerceFrameworkBundle\Type\Decimal;

/**
 * special interface for shipping price modifications - needed for pricing rule that remove shipping costs
 */
interface ShippingInterface extends CartPriceModificatorInterface
{
    public function setCharge(Decimal $charge): CartPriceModificatorInterface;

    public function getCharge(): Decimal;
}
