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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\VoucherService;

use Exception;
use OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\VoucherService\Reservation\Dao;
use OpenDxp\Model\AbstractModel;
use OpenDxp\Model\Exception\NotFoundException;

/**
 * @method Dao getDao()
 */
class Reservation extends AbstractModel
{
    public ?int $id = null;

    public ?string $token = null;

    public ?string $timestamp = null;

    public ?string $cart_id = null;

    public static function get(string $code, CartInterface $cart = null): ?self
    {
        try {
            $config = new self();
            $config->getDao()->get($code, $cart);

            return $config;
        } catch (NotFoundException $ex) {
            //            Logger::debug($ex->getMessage());
            return null;
        }
    }

    public static function create(string $code, CartInterface $cart): ?self
    {
        try {
            $config = new self();
            $config->getDao()->create($code, $cart);

            return $config;
        } catch (Exception $ex) {
            //            Logger::debug($ex->getMessage());
            return null;
        }
    }

    public static function releaseToken(string $code, CartInterface $cart = null): bool
    {
        $db = \OpenDxp\Db::get();

        $query = 'DELETE FROM ' . \OpenDxp\Bundle\EcommerceFrameworkBundle\VoucherService\Reservation\Dao::TABLE_NAME . ' WHERE token = ?';
        $params[] = $code;

        if (isset($cart)) {
            $query .= ' AND cart_id = ?';
            $params[] = $cart->getId();
        }

        try {
            $db->executeQuery($query, $params);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function remove(): bool
    {
        return $this->getDao()->remove();
    }

    /**
     * @param int $duration in Minutes
     */
    public static function cleanUpReservations(int $duration, ?int $seriesId = null): bool
    {
        $query = 'DELETE FROM ' . \OpenDxp\Bundle\EcommerceFrameworkBundle\VoucherService\Reservation\Dao::TABLE_NAME . ' WHERE TIMESTAMPDIFF(MINUTE, timestamp , NOW())  >= ?';
        $params[] = $duration;

        if (isset($seriesId)) {
            $query .= ' AND token in (SELECT token FROM ' . \OpenDxp\Bundle\EcommerceFrameworkBundle\VoucherService\Token\Dao::TABLE_NAME . ' WHERE voucherSeriesId = ?)';
            $params[] = $seriesId;
        }

        $db = \OpenDxp\Db::get();

        try {
            $db->executeQuery($query, $params);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function reservationExists(string $code, CartInterface $cart): bool
    {
        $db = \OpenDxp\Db::get();
        $query = 'SELECT EXISTS(SELECT id FROM ' . \OpenDxp\Bundle\EcommerceFrameworkBundle\VoucherService\Reservation\Dao::TABLE_NAME . ' WHERE token = ? and cart_id = ?)';

        try {
            return (bool)$db->fetchOne($query, [$code, $cart->getId()]);
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getReservationCount(string $code): bool|int
    {
        $db = \OpenDxp\Db::get();
        $query = 'SELECT COUNT(*) FROM ' . \OpenDxp\Bundle\EcommerceFrameworkBundle\VoucherService\Reservation\Dao::TABLE_NAME . ' WHERE token = ? ';

        try {
            $count = $db->fetchOne($query, [$code]);

            return (int)$count;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): void
    {
        $this->token = $token;
    }

    public function getCartId(): ?string
    {
        return $this->cart_id;
    }

    public function setCartId(?string $cart_id): void
    {
        $this->cart_id = $cart_id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getTimestamp(): ?string
    {
        return $this->timestamp;
    }

    public function setTimestamp(?string $timestamp): void
    {
        $this->timestamp = $timestamp;
    }
}
