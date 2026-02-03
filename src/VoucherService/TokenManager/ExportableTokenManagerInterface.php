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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\VoucherService\TokenManager;

interface ExportableTokenManagerInterface
{
    const FORMAT_CSV = 'csv';

    const FORMAT_PLAIN = 'plain';

    /**
     * Export tokens to CSV
     */
    public function exportCsv(array $params): string;

    /**
     * Export tokens to plain text list
     */
    public function exportPlain(array $params): string;
}
