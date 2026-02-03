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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\IndexService\Worker\OpenSearch;

use OpenDxp\Bundle\EcommerceFrameworkBundle\IndexService\ProductList\OpenSearch\DefaultOpenSearch as ProductList;
use OpenDxp\Bundle\EcommerceFrameworkBundle\IndexService\ProductList\ProductListInterface;

class DefaultOpenSearch extends AbstractOpenSearch
{
    public function getProductList(): ProductListInterface
    {
        return new ProductList($this->tenantConfig);
    }
}
