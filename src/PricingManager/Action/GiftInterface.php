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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\Action;

use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractProduct;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\ActionInterface;

/**
 * Adds a gift product to the given cart
 */
interface GiftInterface extends ActionInterface, CartActionInterface
{
    /**
     * Set gift product
     */
    public function setProduct(AbstractProduct $product): GiftInterface;

    public function getProduct(): ?AbstractProduct;
}
