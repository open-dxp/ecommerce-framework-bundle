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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\Condition;

use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\ConditionInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\EnvironmentInterface;

class ClientIp implements ConditionInterface
{
    protected int $ip;

    public function check(EnvironmentInterface $environment): bool
    {
        $clientIp = $_SERVER['HTTP_X_FORWARDED_FOR'] ?: $_SERVER['REMOTE_ADDR'];

        return $clientIp == $this->getIp();
    }

    public function toJSON(): string
    {
        // basic
        $json = [
            'type' => 'ClientIp', 'ip' => $this->getIp(),
        ];

        return json_encode($json);
    }

    public function fromJSON(string $string): ConditionInterface
    {
        $json = json_decode($string);

        $this->setIp($json->ip);

        return $this;
    }

    public function getIp(): int
    {
        return $this->ip;
    }

    public function setIp(int $ip): void
    {
        $this->ip = $ip;
    }
}
