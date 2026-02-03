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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\Model;

use Exception;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Factory;
use OpenDxp\Bundle\EcommerceFrameworkBundle\VoucherService\TokenManager\TokenManagerInterface;

abstract class AbstractVoucherSeries extends \OpenDxp\Model\DataObject\Concrete
{
    abstract public function getTokenSettings(): ?\OpenDxp\Model\DataObject\Fieldcollection;

    public function getTokenManager(): ?TokenManagerInterface
    {
        $items = $this->getTokenSettings();

        if ($items && $items->get(0)) {
            // name of fieldcollection class
            /** @var AbstractVoucherTokenType $configuration */
            $configuration = $items->get(0);

            return Factory::getInstance()->getTokenManager($configuration);
        }

        return null;
    }

    public function getExistingLengths(): bool|array
    {
        $db = \OpenDxp\Db::get();

        $query = '
            SELECT length, COUNT(*) AS count FROM ' . \OpenDxp\Bundle\EcommerceFrameworkBundle\VoucherService\Token\Dao::TABLE_NAME . '
            WHERE voucherSeriesId = ?
            GROUP BY length';

        try {
            $lengths = $db->fetchAllAssociative($query, [$this->getId()]);

            $result = [];
            foreach ($lengths as $lengthEntry) {
                $result[$lengthEntry['length']] = $lengthEntry['count'];
            }

            return $result;
        } catch (Exception $e) {
            return false;
        }
    }
}
