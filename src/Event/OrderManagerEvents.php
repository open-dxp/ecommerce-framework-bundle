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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\Event;

final class OrderManagerEvents
{
    /**
     * @Event("OpenDxp\Bundle\EcommerceFrameworkBundle\Event\Model\OrderManagerEvent")
     *
     * @var string
     */
    const PRE_GET_OR_CREATE_ORDER_FROM_CART = 'opendxp.ecommerce.ordermanager.preGetOrCreateOrderFromCart';

    /**
     * @Event("OpenDxp\Bundle\EcommerceFrameworkBundle\Event\Model\OrderManagerEvent")
     *
     * @var string
     */
    const PRE_UPDATE_ORDER = 'opendxp.ecommerce.ordermanager.preUpdateOrder';

    /**
     * @Event("OpenDxp\Bundle\EcommerceFrameworkBundle\Event\Model\OrderManagerEvent")
     *
     * @var string
     */
    const POST_UPDATE_ORDER = 'opendxp.ecommerce.ordermanager.postUpdateOrder';

    /**
     * @Event("OpenDxp\Bundle\EcommerceFrameworkBundle\Event\Model\OrderManagerItemEvent")
     *
     * @var string
     */
    const POST_CREATE_ORDER_ITEM = 'opendxp.ecommerce.ordermanager.postCreateOrderItem';

    /**
     * @Event("OpenDxp\Bundle\EcommerceFrameworkBundle\Event\Model\OrderManagerItemEvent")
     *
     * @var string
     */
    const BUILD_ORDER_ITEM_KEY = 'opendxp.ecommerce.ordermanager.buildOrderItemKey';
}
