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
use OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartItem;
use OpenDxp\Db;

final class Version20210430124911 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Changes addedDateTimestamp of Cart Items to mirco seconds';
    }

    public function getColumnType(): string
    {
        $db = Db::get();

        return $db->fetchOne(
            'SELECT DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME  = ?',
            [
                $db->getDatabase(),
                'ecommerceframework_cartitem',
                'addedDateTimestamp',
            ]);
    }

    public function up(Schema $schema): void
    {
        if ($schema->hasTable(CartItem\Dao::TABLE_NAME) && $this->getColumnType() === 'int') {
            $this->addSql('ALTER TABLE ecommerceframework_cartitem modify addedDateTimestamp bigint not null;');
            $this->addSql('UPDATE ecommerceframework_cartitem SET addedDateTimestamp = addedDateTimestamp * 1000000;');
        }
    }

    public function down(Schema $schema): void
    {
        if ($schema->hasTable(CartItem\Dao::TABLE_NAME) && $this->getColumnType() === 'bigint') {
            $this->addSql('UPDATE ecommerceframework_cartitem SET addedDateTimestamp = FLOOR(addedDateTimestamp / 1000000);');
            $this->addSql('ALTER TABLE ecommerceframework_cartitem modify addedDateTimestamp int(10) not null;');
        }
    }
}
