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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\IndexService\Config;

use InvalidArgumentException;
use OpenDxp\Bundle\EcommerceFrameworkBundle\IndexService\Worker\DefaultMysql as DefaultMysqlWorker;
use OpenDxp\Bundle\EcommerceFrameworkBundle\IndexService\Worker\WorkerInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\IndexableInterface;

/**
 * Tenant configuration for a simple mysql product index implementation. It is used by the default tenant.
 *
 * @method DefaultMysqlWorker getTenantWorker()
 */
class DefaultMysql extends AbstractConfig implements MysqlConfigInterface
{
    public function getTablename(): string
    {
        return 'ecommerceframework_productindex';
    }

    public function getRelationTablename(): string
    {
        return 'ecommerceframework_productindex_relations';
    }

    public function getTenantRelationTablename(): string
    {
        return '';
    }

    public function getJoins(): string
    {
        return '';
    }

    public function getCondition(): string
    {
        return '';
    }

    public function inIndex(IndexableInterface $object): bool
    {
        return true;
    }

    /**
     * in case of subtenants returns a data structure containing all sub tenants
     *
     *
     * @return mixed $subTenantData
     */
    public function prepareSubTenantEntries(IndexableInterface $object, int $subObjectId = null): mixed
    {
        return null;
    }

    /**
     * populates index for tenant relations based on gived data
     */
    public function updateSubTenantEntries(mixed $objectId, mixed $subTenantData, mixed $subObjectId = null): void
    {
    }

    #[\Override]
    public function setTenantWorker(WorkerInterface $tenantWorker): void
    {
        if (!$tenantWorker instanceof DefaultMysqlWorker) {
            throw new InvalidArgumentException(sprintf(
                'Worker must be an instance of %s',
                DefaultMysqlWorker::class
            ));
        }

        parent::setTenantWorker($tenantWorker);
    }
}
