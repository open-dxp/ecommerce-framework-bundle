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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartCheckoutData;

use OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartCheckoutData;

/**
 * @method CartCheckoutData[] load()
 * @method CartCheckoutData|false current()
 * @method int getTotalCount()
 * @method \Pimcore\Bundle\EcommerceFrameworkBundle\CartManager\CartCheckoutData\Listing\Dao getDao()
 */
class Listing extends \OpenDxp\Model\Listing\AbstractListing
{
    public function isValidOrderKey(string $key): bool
    {
        if ($key == 'key' || $key == 'cartId') {
            return true;
        }

        return false;
    }

    public function getCartCheckoutDataItems(): array
    {
        return $this->getData();
    }

    public function setCartCheckoutDataItems(array $cartCheckoutDataItems): Listing
    {
        return $this->setData($cartCheckoutDataItems);
    }
}
