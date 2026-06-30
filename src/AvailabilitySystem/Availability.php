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

class Availability implements AvailabilityInterface
{
    public function __construct(
        private readonly CheckoutableInterface $product,
        private readonly bool $available
    ) {
    }

    public function getProduct(): CheckoutableInterface
    {
        return $this->product;
    }

    public function getAvailable(): bool
    {
        return $this->available;
    }
}
