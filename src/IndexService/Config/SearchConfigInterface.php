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

use OpenDxp\Bundle\EcommerceFrameworkBundle\IndexService\SynonymProvider\SynonymProviderInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\IndexService\Worker\WorkerInterface;

/**
 * Interface for IndexService Tenant Configurations using elastic search as index
 */
interface SearchConfigInterface extends ConfigInterface
{
    /**
     * returns condition for current subtenant
     */
    public function getSubTenantCondition(): array;

    /**
     * creates and returns tenant worker suitable for this tenant configuration
     */
    public function getTenantWorker(): WorkerInterface;

    /**
     * Get an associative array of configured synonym providers.
     *  - key: the name of the synonym provider configuration, which is equivalent to the name of the configured filter
     *  - value: the synonym provider
     *
     * @return SynonymProviderInterface[]
     */
    public function getSynonymProviders(): array;

    public function getClientConfig(string $property = null): array|string|null;

    /**
     * returns the full field name
     *
     * @param bool $considerSubFieldNames - activate to consider subfield names like name.analyzed or score definitions like name^3
     */
    public function getFieldNameMapped(string $fieldName, bool $considerSubFieldNames = false): string;

    /**
     * returns short field name based on full field name
     * also considers subfield names like name.analyzed etc.
     *
     *
     * @return false|int|string
     */
    public function getReverseMappedFieldName(string $fullFieldName): bool|int|string;
}
