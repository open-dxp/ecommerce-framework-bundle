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

use DateTime;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Exception\InvalidConfigException;
use stdClass;

/**
 * Sample implementation for delivery date
 */
class DeliveryDate extends AbstractStep implements CheckoutStepInterface
{
    const INSTANTLY = 'delivery_instantly';

    const DATE = 'delivery_date';

    public function getName(): string
    {
        return 'deliverydate';
    }

    public function commit(mixed $data): bool
    {
        if (empty($data->instantly) && empty($data->date)) {
            throw new InvalidConfigException('Instantly or Date not set.');
        }

        $this->cart->setCheckoutData(self::INSTANTLY, $data->instantly);

        $date = null;
        if ($data->date instanceof DateTime) {
            $date = $data->date->getTimestamp();
        }

        $this->cart->setCheckoutData(self::DATE, (string) $date);

        return true;
    }

    public function getData(): mixed
    {
        $data = new stdClass();
        $data->instantly = $this->cart->getCheckoutData(self::INSTANTLY);

        if ($this->cart->getCheckoutData(self::DATE)) {
            $data->date = new DateTime();
            $data->date->setTimestamp((int) $this->cart->getCheckoutData(self::DATE));
        } else {
            $data->instantly = true;
        }

        return $data;
    }
}
