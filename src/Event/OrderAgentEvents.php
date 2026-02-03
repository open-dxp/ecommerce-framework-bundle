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

final class OrderAgentEvents
{
    /**
     * @Event("OpenDxp\Bundle\EcommerceFrameworkBundle\Event\Model\OrderAgentEvent")
     *
     * @var string
     */
    const PRE_INIT_PAYMENT = 'opendxp.ecommerce.orderagent.preInitPayment';

    /**
     * @Event("OpenDxp\Bundle\EcommerceFrameworkBundle\Event\Model\OrderAgentEvent")
     *
     * @var string
     */
    const POST_INIT_PAYMENT = 'opendxp.ecommerce.orderagent.postInitPayment';

    /**
     * @Event("OpenDxp\Bundle\EcommerceFrameworkBundle\Event\Model\OrderAgentEvent")
     *
     * @var string
     */
    const PRE_START_PAYMENT = 'opendxp.ecommerce.orderagent.preStartPayment';

    /**
     * @Event("OpenDxp\Bundle\EcommerceFrameworkBundle\Event\Model\OrderAgentEvent")
     *
     * @var string
     */
    const POST_START_PAYMENT = 'opendxp.ecommerce.orderagent.postStartPayment';

    /**
     * @Event("OpenDxp\Bundle\EcommerceFrameworkBundle\Event\Model\OrderAgentEvent")
     *
     * @var string
     */
    const PRE_CANCEL_PAYMENT = 'opendxp.ecommerce.orderagent.preCancelPayment';

    /**
     * @Event("OpenDxp\Bundle\EcommerceFrameworkBundle\Event\Model\OrderAgentEvent")
     *
     * @var string
     */
    const POST_CANCEL_PAYMENT = 'opendxp.ecommerce.orderagent.postCancelPayment';

    /**
     * @Event("OpenDxp\Bundle\EcommerceFrameworkBundle\Event\Model\OrderAgentEvent")
     *
     * @var string
     */
    const FINGERPRINT_GENERATED = 'opendxp.ecommerce.orderagent.fingerPrintGenerated';

    /**
     * @Event("OpenDxp\Bundle\EcommerceFrameworkBundle\Event\Model\OrderAgentEvent")
     *
     * @var string
     */
    const PRE_UPDATE_PAYMENT = 'opendxp.ecommerce.orderagent.preUpdatePayment';

    /**
     * @Event("OpenDxp\Bundle\EcommerceFrameworkBundle\Event\Model\OrderAgentEvent")
     *
     * @var string
     */
    const POST_UPDATE_PAYMENT = 'opendxp.ecommerce.orderagent.postUpdatePayment';
}
