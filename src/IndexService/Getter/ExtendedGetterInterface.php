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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\IndexService\Getter;

use OpenDxp\Bundle\EcommerceFrameworkBundle\IndexService\Config\ConfigInterface;

/**
 * Interface for getter of product index columns which consider sub object ids and tenant configs
 */
interface ExtendedGetterInterface extends GetterInterface
{
    public function get(object $object, array $config = null, ?int $subObjectId = null, ?ConfigInterface $tenantConfig = null): mixed;
}
