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

use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\IndexableInterface;
use Override;

/**
 * Sample implementation for sub-tenants based on elastic search.
 */
class DefaultElasticSearchSubTenantConfig extends ElasticSearch
{
    /**
     * checks, if product should be in index for current tenant (not subtenant)
     */
    #[Override]
    public function inIndex(IndexableInterface $object): bool
    {
        $tenants = null;
        if (method_exists($object, 'getTenants')) {
            $tenants = $object->getTenants();
        }

        return !empty($tenants);
    }

    /**
     * in case of subtenants returns an array containing all sub tenants
     *
     * In this case tenants are also Pimcore objects and are assigned to product objects.
     * This method extracts assigned tenants and returns an array of subtenant-IDs
     *
     *
     * @return array $subTenantData
     */
    #[Override]
    public function prepareSubTenantEntries(IndexableInterface $object, ?int $subObjectId = null): array
    {
        $subTenantData = [];
        if ($this->inIndex($object)) {
            $tenants = [];
            if (method_exists($object, 'getTenants')) {
                $tenants = $object->getTenants();
            }

            //implementation specific tenant get logic
            foreach ($tenants as $tenant) {
                $subTenantData[] = $tenant->getId();
            }
        }

        return ['ids' => $subTenantData];
    }
}
