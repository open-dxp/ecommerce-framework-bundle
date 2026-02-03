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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\VoucherService\Statistic;

use OpenDxp\Model\Exception\NotFoundException;

/**
 * @internal
 */
class Dao extends \OpenDxp\Model\Dao\AbstractDao
{
    const TABLE_NAME = 'ecommerceframework_vouchertoolkit_statistics';

    public function __construct()
    {
        $this->db = \OpenDxp\Db::get();
    }

    /**
     * @throws NotFoundException
     */
    public function getById(int $id): bool|string
    {
        $result = $this->db->fetchOne('SELECT * FROM ' . self::TABLE_NAME . ' WHERE id = ? GROUP BY date', [$id]);
        if (empty($result)) {
            throw new NotFoundException('Statistic with id ' . $id . ' not found.');
        }
        $this->assignVariablesToModel($result);

        return $result;
    }
}
