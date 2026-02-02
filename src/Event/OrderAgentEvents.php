<?php
declare(strict_types=1);

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Commercial License (PCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PCL
 */

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\Event;

final class OrderAgentEvents
{
    /**
     * @Event("Pimcore\Bundle\EcommerceFrameworkBundle\Event\Model\OrderAgentEvent")
     *
     * @var string
     */
    const PRE_INIT_PAYMENT = 'opendxp.ecommerce.orderagent.preInitPayment';

    /**
     * @Event("Pimcore\Bundle\EcommerceFrameworkBundle\Event\Model\OrderAgentEvent")
     *
     * @var string
     */
    const POST_INIT_PAYMENT = 'opendxp.ecommerce.orderagent.postInitPayment';

    /**
     * @Event("Pimcore\Bundle\EcommerceFrameworkBundle\Event\Model\OrderAgentEvent")
     *
     * @var string
     */
    const PRE_START_PAYMENT = 'opendxp.ecommerce.orderagent.preStartPayment';

    /**
     * @Event("Pimcore\Bundle\EcommerceFrameworkBundle\Event\Model\OrderAgentEvent")
     *
     * @var string
     */
    const POST_START_PAYMENT = 'opendxp.ecommerce.orderagent.postStartPayment';

    /**
     * @Event("Pimcore\Bundle\EcommerceFrameworkBundle\Event\Model\OrderAgentEvent")
     *
     * @var string
     */
    const PRE_CANCEL_PAYMENT = 'opendxp.ecommerce.orderagent.preCancelPayment';

    /**
     * @Event("Pimcore\Bundle\EcommerceFrameworkBundle\Event\Model\OrderAgentEvent")
     *
     * @var string
     */
    const POST_CANCEL_PAYMENT = 'opendxp.ecommerce.orderagent.postCancelPayment';

    /**
     * @Event("Pimcore\Bundle\EcommerceFrameworkBundle\Event\Model\OrderAgentEvent")
     *
     * @var string
     */
    const FINGERPRINT_GENERATED = 'opendxp.ecommerce.orderagent.fingerPrintGenerated';

    /**
     * @Event("Pimcore\Bundle\EcommerceFrameworkBundle\Event\Model\OrderAgentEvent")
     *
     * @var string
     */
    const PRE_UPDATE_PAYMENT = 'opendxp.ecommerce.orderagent.preUpdatePayment';

    /**
     * @Event("Pimcore\Bundle\EcommerceFrameworkBundle\Event\Model\OrderAgentEvent")
     *
     * @var string
     */
    const POST_UPDATE_PAYMENT = 'opendxp.ecommerce.orderagent.postUpdatePayment';
}
