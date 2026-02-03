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

final class CheckoutManagerEvents
{
    /**
     * @Event("OpenDxp\Bundle\EcommerceFrameworkBundle\Event\Model\CheckoutManagerStepsEvent")
     */
    const string PRE_COMMIT_STEP = 'opendxp.ecommerce.checkoutmanager.preCommitStep';

    /**
     * @Event("OpenDxp\Bundle\EcommerceFrameworkBundle\Event\Model\CheckoutManagerStepsEvent")
     */
    const string POST_COMMIT_STEP = 'opendxp.ecommerce.checkoutmanager.postCommitStep';

    /**
     * @Event("OpenDxp\Bundle\EcommerceFrameworkBundle\Event\Model\CheckoutManagerStepsEvent")
     */
    const string INITIALIZE_STEP_STATE = 'opendxp.ecommerce.checkoutmanager.initializeStepState';
}
