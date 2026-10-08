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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use OpenDxp\Bundle\EcommerceFrameworkBundle\OpenDxpEcommerceFrameworkBundle;
use Override;

final class Version20261005120000 extends AbstractMigration
{
    #[Override]
    public function getDescription(): string
    {
        return 'Marks an installation as installed in the settings store, where the installer now looks for it.';
    }

    #[Override]
    public function up(Schema $schema): void
    {
        $this->addSql(
            'INSERT INTO settings_store (id, scope, type, data)
                SELECT ?, ?, ?, ? FROM users_permission_definitions WHERE `key` = ?
                ON DUPLICATE KEY UPDATE data = VALUES(data);',
            ['BUNDLE_INSTALLED__' . OpenDxpEcommerceFrameworkBundle::class, 'opendxp', 'bool', '1', 'bundle_ecommerce_pricing_rules'],
        );
    }

    #[Override]
    public function down(Schema $schema): void
    {
    }
}
