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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\OfferTool;

use OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartItemInterface;

interface ServiceInterface
{
    const DISCOUNT_TYPE_PERCENT = 'percent';

    const DISCOUNT_TYPE_AMOUNT = 'amount';

    /**
     * @param CartItemInterface[] $excludeItems
     */
    public function createNewOfferFromCart(CartInterface $cart, array $excludeItems = []): AbstractOffer;

    public function updateOfferFromCart(AbstractOffer $offer, CartInterface $cart, array $excludeItems = []): AbstractOffer;

    public function updateTotalPriceOfOffer(AbstractOffer $offer): AbstractOffer;

    /**
     * @return AbstractOffer[]
     */
    public function getOffersForCart(CartInterface $cart): array;

    public function getNewOfferItemObject(): AbstractOfferItem;
}
