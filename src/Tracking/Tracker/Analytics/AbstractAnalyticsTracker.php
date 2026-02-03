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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\Tracking\Tracker\Analytics;

use OpenDxp\Bundle\EcommerceFrameworkBundle\Tracking\Tracker as EcommerceTracker;

use OpenDxp\Bundle\EcommerceFrameworkBundle\Tracking\TrackingItemBuilderInterface;
use OpenDxp\Bundle\GoogleMarketingBundle\Tracker\TrackerInterface;
use Twig\Environment;

abstract class AbstractAnalyticsTracker extends EcommerceTracker
{
    /**
     * @internal
     */
    public function __construct(
        TrackingItemBuilderInterface $trackingItemBuilder,
        Environment $twig,
        protected TrackerInterface $tracker,
        array $options = [],
        array $assortmentTenants = [],
        array $checkoutTenants = []
    ) {
        parent::__construct($trackingItemBuilder, $twig, $options, $assortmentTenants, $checkoutTenants);
    }
}
