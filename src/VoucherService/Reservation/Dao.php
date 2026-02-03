<?php

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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\VoucherService\Reservation;

// TODO - Log Exceptions

use Exception;
use OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\VoucherService\Reservation;
use OpenDxp\Model\Exception\NotFoundException;

/**
 * @internal
 *
 * @property Reservation $model
 */
class Dao extends \OpenDxp\Model\Dao\AbstractDao
{
    const TABLE_NAME = 'ecommerceframework_vouchertoolkit_reservations';

    public function __construct()
    {
        $this->db = \OpenDxp\Db::get();
    }

    /**
     * @throws NotFoundException
     */
    public function get(string $code, ?CartInterface $cart = null): void
    {
        $query = 'SELECT * FROM ' . self::TABLE_NAME . ' WHERE token = ?';
        $params[] = $code;
        if (isset($cart)) {
            $query .= ' AND cart_id = ?';
            $params[] = $cart->getId();
        }

        $result = $this->db->fetchAssociative($query, $params);
        if (empty($result)) {
            throw new NotFoundException('Reservation for token ' . $code . ' not found.');
        }
        $this->assignVariablesToModel($result);
        $this->model->setValue('id', $result['id']);
        $this->model->setCartId($result['cart_id']);
    }

    public function create(string $code, CartInterface $cart): void
    {
        if (!Reservation::reservationExists($code, $cart)) {
            // Single Type Token --> only one token per Cart! --> Update on duplicate key!
            $this->db->executeQuery('INSERT INTO ' . self::TABLE_NAME . ' (token,cart_id,timestamp) VALUES (?,?,NOW())', [$code, $cart->getId()]);
        }

        $this->get($code, $cart);
    }

    public function remove(): bool
    {
        $this->db->delete(self::TABLE_NAME, ['token' => $this->model->getToken()]);

        return true;
    }

    public static function getReservedTokenCount(?int $seriesId = null): bool|int
    {
        $db = \OpenDxp\Db::get();

        $query = 'SELECT COUNT(*) FROM ' . self::TABLE_NAME;
        $params = [];

        if (isset($seriesId)) {
            $query .= ' WHERE seriesId = ?';
            $params[] = $seriesId;
        }

        try {
            $count = $db->fetchOne($query, $params);
            if ($count === 0) {
                return false;
            }

            return $count;
        } catch (Exception) {
            return true;
        }
    }

    public static function isReservedToken(string $token): bool
    {
        $db = \OpenDxp\Db::get();

        $query = 'SELECT isReserved FROM ' . self::TABLE_NAME . ' WHERE token = ? ';
        $params[] = $token;

        try {
            return $db->fetchOne($query, $params) !== 0;
        } catch (Exception) {
            return true;
        }
    }
}
