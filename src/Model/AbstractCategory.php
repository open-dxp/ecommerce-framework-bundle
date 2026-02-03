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

/**
 * Abstract base class for pimcore objects who should be used as product categories in the online shop framework
 */
class AbstractCategory extends \OpenDxp\Model\DataObject\Concrete
{
    /**
     * defines if product is visible in product index queries for parent categories of product category.
     * e.g.
     *   football
     *     - shoes
     *     - shirts
     *
     * all products if category shoes or shirts are visible in queries for category football
     */
    public function getOSProductsInParentCategoryVisible(): bool
    {
        return true;
    }
}
