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

use OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartPriceModificator\ShippingInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\ActionInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\EnvironmentInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Type\Decimal;

class FreeShipping implements ActionInterface, CartActionInterface
{
    public function executeOnCart(EnvironmentInterface $environment): ActionInterface
    {
        $priceCalculator = $environment->getCart()->getPriceCalculator();

        $list = $priceCalculator->getModificators();
        foreach ($list as &$modificator) {
            // remove shipping charge
            if ($modificator instanceof ShippingInterface) {
                $modificator->setCharge(Decimal::zero());
                $priceCalculator->calculate(true);
            }
        }

        return $this;
    }

    public function toJSON(): string
    {
        return json_encode([
            'type' => 'FreeShipping',
        ]);
    }

    public function fromJSON(string $string): ActionInterface
    {
        return $this;
    }
}
