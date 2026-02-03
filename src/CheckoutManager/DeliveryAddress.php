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

/**
 * Sample implementation for delivery address
 */
class DeliveryAddress extends AbstractStep implements CheckoutStepInterface
{
    /**
     * Namespace key
     */
    const PRIVATE_NAMESPACE = 'delivery_address';

    public function getName(): string
    {
        return 'deliveryaddress';
    }

    public function commit(mixed $data): bool
    {
        $this->cart->setCheckoutData(self::PRIVATE_NAMESPACE, json_encode($data));

        return true;
    }

    public function getData(): mixed
    {
        $data = json_decode((string) $this->cart->getCheckoutData(self::PRIVATE_NAMESPACE));

        return $data;
    }
}
