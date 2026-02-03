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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\CheckoutManager;

use OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartInterface;

/**
 * Interface for checkout step implementations of online shop framework
 */
interface CheckoutStepInterface
{
    public function __construct(CartInterface $cart, array $options = []);

    /**
     * Returns checkout step name
     */
    public function getName(): string;

    /**
     * Returns saved data of step
     */
    public function getData(): mixed;

    /**
     * Sets delivered data and commits step
     */
    public function commit(mixed $data): bool;
}
