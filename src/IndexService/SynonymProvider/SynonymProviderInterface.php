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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\IndexService\SynonymProvider;

interface SynonymProviderInterface
{
    /**
     * Get synonyms, depending on the format that is specified in the filter.
     * Typically Solr is used, compare https://www.elastic.co/guide/en/elasticsearch/reference/current/analysis-synonym-tokenfilter.html.
     * Examples:
     *      - line 1: i-pod, i pod => ipod
     *      - line 2: sea biscuit, sea biscit => seabiscuit
     *      - ...
     *
     * @return string[] an array, where each array element corresponds to one line of related synonyms.
     */
    public function getSynonyms(): array;

    /**
     * return a list of options that can be configured per options provider and can be used for the
     * implementation of the synonym provider.
     */
    public function getOptions(): array;
}
