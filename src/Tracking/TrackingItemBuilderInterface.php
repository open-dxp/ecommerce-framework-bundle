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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\Tracking;

use OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractOrder;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractOrderItem;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\ProductInterface;

interface TrackingItemBuilderInterface
{
    /**
     * Build a product view object
     */
    public function buildProductViewItem(ProductInterface $product): ProductAction;

    /**
     * Build a product action item object
     */
    public function buildProductActionItem(ProductInterface $product, int $quantity = 1): ProductAction;

    /**
     * Build a product impression object
     */
    public function buildProductImpressionItem(ProductInterface $product, string $list = 'default'): ProductImpression;

    /**
     * Build a checkout transaction object
     */
    public function buildCheckoutTransaction(AbstractOrder $order): Transaction;

    /**
     * Build checkout items
     *
     *
     * @return ProductAction[]
     */
    public function buildCheckoutItems(AbstractOrder $order): array;

    /**
     * Build checkout items by cart
     *
     *
     * @return ProductAction[]
     */
    public function buildCheckoutItemsByCart(CartInterface $cart): array;

    /**
     * Build a checkout item object
     */
    public function buildCheckoutItem(AbstractOrder $order, AbstractOrderItem $orderItem): ProductAction;
}
