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

final class CommitOrderProcessorEvents
{
    /**
     * @Event("OpenDxp\Bundle\EcommerceFrameworkBundle\Event\Model\CommitOrderProcessorEvent")
     *
     * @var string
     */
    const PRE_COMMIT_ORDER_PAYMENT = 'opendxp.ecommerce.commitorderprocessor.preCommitOrderPayment';

    /**
     * @Event("OpenDxp\Bundle\EcommerceFrameworkBundle\Event\Model\CommitOrderProcessorEvent")
     *
     * @var string
     */
    const POST_COMMIT_ORDER_PAYMENT = 'opendxp.ecommerce.commitorderprocessor.postCommitOrderPayment';

    /**
     * @Event("OpenDxp\Bundle\EcommerceFrameworkBundle\Event\Model\CommitOrderProcessorEvent")
     *
     * @var string
     */
    const PRE_COMMIT_ORDER = 'opendxp.ecommerce.commitorderprocessor.preCommitOrder';

    /**
     * @Event("OpenDxp\Bundle\EcommerceFrameworkBundle\Event\Model\CommitOrderProcessorEvent")
     *
     * @var string
     */
    const POST_COMMIT_ORDER = 'opendxp.ecommerce.commitorderprocessor.postCommitOrder';

    /**
     * @Event("OpenDxp\Bundle\EcommerceFrameworkBundle\Event\Model\CommitOrderProcessorEvent")
     *
     * @var string
     */
    const PRE_CLEANUP_PENDING_ORDER = 'opendxp.ecommerce.commitorderprocessor.preCleanupPendingOrder';

    /**
     * @Event("OpenDxp\Bundle\EcommerceFrameworkBundle\Event\Model\CommitOrderProcessorEvent")
     *
     * @var string
     */
    const PRE_CLEANUP_PENDING_PAYMENT = 'opendxp.ecommerce.commitorderprocessor.preCleanupPendingPayment';

    /**
     * @Event("OpenDxp\Bundle\EcommerceFrameworkBundle\Event\Model\SendConfirmationMailEvent")
     *
     * @var string
     */
    const SEND_CONFIRMATION_MAILS = 'opendxp.ecommerce.commitorderprocessor.sendConfirmationMails';
}
