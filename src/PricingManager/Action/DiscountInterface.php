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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\Action;

use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\ActionInterface;

interface DiscountInterface extends ActionInterface
{
    public function setAmount(float $amount): void;

    public function setPercent(float $percent): void;

    public function getAmount(): float;

    public function getPercent(): float;
}
