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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\IndexService\Config;

use OpenDxp\Bundle\EcommerceFrameworkBundle\IndexService\Worker\DefaultFindologic as DefaultFindologicWorker;

/**
 * Interface for IndexService Tenant Configurations using findologic as index
 */
interface FindologicConfigInterface extends ConfigInterface
{
    /**
     * returns findologic client parameters defined in the tenant config
     */
    public function getClientConfig(?string $setting = null): array|string|null;

    /**
     * returns condition for current subtenant
     */
    public function getSubTenantCondition(): array;

    /**
     * creates and returns tenant worker suitable for this tenant configuration
     */
    public function getTenantWorker(): DefaultFindologicWorker;
}
