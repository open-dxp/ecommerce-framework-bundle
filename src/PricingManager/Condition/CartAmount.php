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
use OpenDxp\Bundle\EcommerceFrameworkBundle\Type\Decimal;

class CartAmount implements CartAmountInterface
{
    protected float $limit;

    public function check(EnvironmentInterface $environment): bool
    {
        if (!$environment->getCart() || $environment->getProduct() !== null) {
            return false;
        }

        $calculator = $environment->getCart()->getPriceCalculator();

        // TODO store limit as Decimal?
        return $calculator->getSubTotal()->getAmount()->greaterThanOrEqual(Decimal::create($this->getLimit()));
    }

    public function setLimit(float $limit): CartAmountInterface
    {
        $this->limit = $limit;

        return $this;
    }

    public function getLimit(): float
    {
        return $this->limit;
    }

    public function toJSON(): string
    {
        return json_encode([
            'type' => 'CartAmount',
            'limit' => $this->getLimit(),
        ]);
    }

    public function fromJSON(string $string): ConditionInterface
    {
        $json = json_decode($string);
        $this->setLimit($json->limit);

        return $this;
    }
}
