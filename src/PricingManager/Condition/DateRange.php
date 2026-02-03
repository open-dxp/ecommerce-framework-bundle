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

use DateTime;
use DateTimeZone;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\ConditionInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\EnvironmentInterface;

class DateRange implements DateRangeInterface
{
    protected ?DateTime $starting = null;

    protected ?DateTime $ending = null;

    public function check(EnvironmentInterface $environment): bool
    {
        if ($this->getStarting() && $this->getEnding()) {
            return $this->getStarting()->getTimestamp() < time() && $this->getEnding()->getTimestamp() > time();
        }

        return false;
    }

    public function setStarting(DateTime $date): DateRangeInterface
    {
        $this->starting = $date;

        return $this;
    }

    public function setEnding(DateTime $date): DateRangeInterface
    {
        $this->ending = $date;

        return $this;
    }

    public function getStarting(): ?DateTime
    {
        return $this->starting;
    }

    public function getEnding(): ?DateTime
    {
        return $this->ending;
    }

    public function toJSON(): string
    {
        return json_encode([
            'type' => 'DateRange',
            'starting' => $this->getStarting()?->format('d.m.Y'),
            'ending' => $this->getEnding()?->format('d.m.Y'),
        ]);
    }

    public function fromJSON(string $string): ConditionInterface
    {
        $json = json_decode($string);

        $starting = DateTime::createFromFormat('d.m.Y', $json->starting, new DateTimeZone('UTC'));
        if ($starting instanceof DateTime) {
            $starting->setTime(0, 0, 0);
            $this->setStarting($starting);
        }

        $ending = DateTime::createFromFormat('d.m.Y', $json->ending, new DateTimeZone('UTC'));
        if ($ending instanceof DateTime) {
            $ending->setTime(23, 59, 59);
            $this->setEnding($ending);
        }

        return $this;
    }
}
