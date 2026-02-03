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

interface TrackingManagerInterface extends
    CategoryPageViewInterface,
    ProductImpressionInterface,
    ProductViewInterface,
    CartUpdateInterface,
    CartProductActionAddInterface,
    CartProductActionRemoveInterface,
    CheckoutInterface,
    CheckoutStepInterface,
    CheckoutCompleteInterface,
    TrackEventInterface
{
    /**
     * Returns the current javascript tracking codes for all trackers
     */
    public function getTrackedCodes(): string;

    /**
     * Forwards all tracked tracking codes to the next request via FlashMesssageBag
     *
     * @return $this
     */
    public function forwardTrackedCodesAsFlashMessage(): static;
}
