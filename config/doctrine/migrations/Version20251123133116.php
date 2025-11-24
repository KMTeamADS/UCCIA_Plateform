<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251123133116 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE app_menu_item_translations (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', translatable_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, url VARCHAR(255) DEFAULT NULL, locale VARCHAR(5) NOT NULL, INDEX IDX_9C944AF2C2AC5D3 (translatable_id), UNIQUE INDEX app_menu_item_translations_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE app_menu_items (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', menu_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', page_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', type VARCHAR(50) DEFAULT \'page\' NOT NULL, new_window TINYINT(1) DEFAULT 0 NOT NULL, position INT DEFAULT 0 NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', enabled TINYINT(1) DEFAULT 1 NOT NULL, INDEX IDX_26A96156CCD7E912 (menu_id), INDEX IDX_26A96156C4663E4 (page_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE app_menu_item_translations ADD CONSTRAINT FK_9C944AF2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES app_menu_items (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE app_menu_items ADD CONSTRAINT FK_26A96156CCD7E912 FOREIGN KEY (menu_id) REFERENCES app_menus (id)');
        $this->addSql('ALTER TABLE app_menu_items ADD CONSTRAINT FK_26A96156C4663E4 FOREIGN KEY (page_id) REFERENCES app_pages (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE app_menu_item_translations DROP FOREIGN KEY FK_9C944AF2C2AC5D3');
        $this->addSql('ALTER TABLE app_menu_items DROP FOREIGN KEY FK_26A96156CCD7E912');
        $this->addSql('ALTER TABLE app_menu_items DROP FOREIGN KEY FK_26A96156C4663E4');
        $this->addSql('DROP TABLE app_menu_item_translations');
        $this->addSql('DROP TABLE app_menu_items');
    }
}
