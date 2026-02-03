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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\IndexService\Worker\ElasticSearch;

/**
 *  Use this for ES Version = 8
 */
class DefaultElasticSearch8 extends AbstractElasticSearch
{
    public function getProductList(): \OpenDxp\Bundle\EcommerceFrameworkBundle\IndexService\ProductList\ElasticSearch\DefaultElasticSearch8
    {
        return new \OpenDxp\Bundle\EcommerceFrameworkBundle\IndexService\ProductList\ElasticSearch\DefaultElasticSearch8($this->tenantConfig);
    }
}
