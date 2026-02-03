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

final class AdminEvents
{
    /**
     * Fired when values in filter definition get fetched
     *
     * @Event("Symfony\Component\EventDispatcher\GenericEvent")
     */
    const string GET_VALUES_FOR_FILTER_FIELD_PRE_SEND_DATA = 'opendxp.admin.ecommerce.getValuesForFilterFieldPreSendData';

    /**
     * Fired when filter fields get fetched
     *
     * @Event("Symfony\Component\EventDispatcher\GenericEvent")
     */
    const string GET_INDEX_FIELD_NAMES_PRE_SEND_DATA = 'opendxp.admin.ecommerce.getIndexFieldNamesPreSendData';
}
