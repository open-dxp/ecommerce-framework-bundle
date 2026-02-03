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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager;

use Exception;
use OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartCheckoutData\Dao;
use OpenDxp\Cache\RuntimeCache;
use OpenDxp\Logger;
use OpenDxp\Model\Exception\NotFoundException;

/**
 * @method Dao getDao()
 */
class CartCheckoutData extends AbstractCartCheckoutData
{
    public function save(): void
    {
        $this->getDao()->save();
    }

    public static function getByKeyCartId(string $key, int|string $cartId): ?AbstractCartCheckoutData
    {
        $cacheKey = CartCheckoutData\Dao::TABLE_NAME . '_' . $key . '_' . $cartId;

        try {
            $checkoutDataItem = RuntimeCache::get($cacheKey);
        } catch (Exception) {
            try {
                $checkoutDataItem = new self();
                $checkoutDataItem->getDao()->getByKeyCartId($key, $cartId);
                RuntimeCache::set($cacheKey, $checkoutDataItem);
            } catch (NotFoundException $ex) {
                Logger::debug($ex->getMessage());

                return null;
            }
        }

        return $checkoutDataItem;
    }

    public static function removeAllFromCart(int|string $cartId): void
    {
        $checkoutDataItem = new self();
        $checkoutDataItem->getDao()->removeAllFromCart($cartId);
    }
}
