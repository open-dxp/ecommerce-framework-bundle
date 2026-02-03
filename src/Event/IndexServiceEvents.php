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

final class IndexServiceEvents
{
    /**
     * Fired when error occurs during processing attributes for index. Event can influence handling of that error (like ignoring, throwing exceptions, etc.)
     *
     * @Event("OpenDxp\Bundle\EcommerceFrameworkBundle\Event\Model\IndexService\PreprocessAttributeErrorEvent")
     *
     * @var string
     */
    const ATTRIBUTE_PROCESSING_ERROR = 'opendxp.ecommerce.indexservice.preProcessAttributeError';

    /**
     * Fired when error occurs during pre processing index data. Event can influence handling of that error (like throwing exceptions, etc.)
     *
     * @Event("OpenDxp\Bundle\EcommerceFrameworkBundle\Event\Model\IndexService\PreprocessErrorEvent")
     *
     * @var string
     */
    const GENERAL_PREPROCESSING_ERROR = 'opendxp.ecommerce.indexservice.generalPreProcessingError';
}
