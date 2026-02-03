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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\AvailabilitySystem;

use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\CheckoutableInterface;

interface AvailabilitySystemInterface
{
    /**
     * Get availability info for a given product
     */
    public function getAvailabilityInfo(CheckoutableInterface $product, int $quantityScale = 1, array $products = null): AvailabilityInterface;
}
