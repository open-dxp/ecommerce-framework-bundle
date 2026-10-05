<?php

declare(strict_types=1);

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
