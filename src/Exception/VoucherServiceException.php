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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\Exception;

/**
 * Class VoucherServiceException
 */
class VoucherServiceException extends AbstractEcommerceException
{
    const ERROR_CODE_TOKEN_ALREADY_IN_USE = 1;

    const ERROR_CODE_TOKEN_ALREADY_RESERVED = 2;

    const ERROR_CODE_TOKEN_RESERVATION_NOT_POSSIBLE = 3;

    const ERROR_CODE_NO_TOKEN_FOR_THIS_CODE_EXISTS = 4;

    const ERROR_CODE_ONCE_PER_CART_VIOLATED = 5;

    const ERROR_CODE_ONLY_TOKEN_PER_CART_CANNOT_BE_ADDED = 6;

    const ERROR_CODE_ONLY_TOKEN_PER_CART_ALREADY_ADDED = 7;

    const ERROR_CODE_NO_MORE_USAGES = 8;

    const ERROR_CODE_NOT_FOUND_IN_CART = 9;
}
