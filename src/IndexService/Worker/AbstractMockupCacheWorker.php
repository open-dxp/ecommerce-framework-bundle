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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\IndexService\Worker;

use OpenDxp\Bundle\EcommerceFrameworkBundle\Exception\InvalidConfigException;
use OpenDxp\Bundle\EcommerceFrameworkBundle\IndexService\Config\MockupConfigInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\DefaultMockup;
use OpenDxp\Cache;
use OpenDxp\Logger;

/**
 * provides worker functionality for mockup cache and central store table
 */
abstract class AbstractMockupCacheWorker extends ProductCentricBatchProcessingWorker
{
    /**
     * returns prefix for cache key
     */
    abstract protected function getMockupCachePrefix(): string;

    /**
     * creates mockup cache key
     */
    protected function createMockupCacheKey(int $objectId): string
    {
        return $this->getMockupCachePrefix() . '_' . $this->name . '_' . $objectId;
    }

    /**
     * deletes element from mockup cache
     */
    protected function deleteFromMockupCache(int $objectId): void
    {
        $key = $this->getMockupCachePrefix() . '_' . $this->name . '_' . $objectId;
        Cache::remove($key);
    }

    /**
     * updates mockup cache, delegates creation of mockup object to tenant config
     *
     *
     *
     * @throws InvalidConfigException
     */
    public function saveToMockupCache(int $objectId, ?array $data = null): DefaultMockup
    {
        if (empty($data)) {
            $data = $this->db->fetchOne('SELECT data FROM ' . $this->getStoreTableName() . ' WHERE id = ? AND tenant = ?', [$objectId, $this->name]);
            $data = json_decode($data, true);
        }

        if ($this->tenantConfig instanceof MockupConfigInterface) {
            $mockup = $this->tenantConfig->createMockupObject($objectId, $data['data'], $data['relations']);
        } else {
            throw new InvalidConfigException('Tenant Config is not instance of MockupConfigInterface');
        }

        $key = $this->createMockupCacheKey($objectId);

        //use cache instance directly to avoid cache locking -> in this case force writing to cache is needed
        $hasLock = Cache::getHandler()->getWriteLock()->hasLock();
        if ($hasLock) {
            Cache::getHandler()->getWriteLock()->disable();
        }

        $success = Cache::save($mockup, $key, [$this->getMockupCachePrefix()], null, 0, true);
        $result = Cache::load($key);

        if ($success && $result) {
            $this->executeTransactionalQuery(function () use ($objectId): void {
                $this->db->executeQuery('UPDATE ' . $this->getStoreTableName() . ' SET crc_index = crc_current WHERE id = ? and tenant = ?', [$objectId, $this->name]);
            });
        } else {
            Logger::err("Element with ID $objectId could not be added to mockup-cache");
        }

        if ($hasLock) {
            Cache::getHandler()->getWriteLock()->enable();
        }

        return $mockup;
    }

    /**
     * gets mockup from cache and if not in cache, adds it to cache
     */
    public function getMockupFromCache(int $objectId): DefaultMockup
    {
        $key = $this->createMockupCacheKey($objectId);
        $cachedItem = Cache::load($key);

        if (is_string($cachedItem)) {
            $cachedItem = unserialize($cachedItem);
        }
        if ($cachedItem instanceof DefaultMockup) {
            return $cachedItem;
        }

        Logger::info("Element with ID $objectId was not found in cache, trying to put it there.");

        return $this->saveToMockupCache($objectId);
    }
}
