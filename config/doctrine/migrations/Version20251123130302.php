<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251123130302 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE app_menu_translations (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', translatable_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, locale VARCHAR(5) NOT NULL, INDEX IDX_18F086BD2C2AC5D3 (translatable_id), UNIQUE INDEX app_menu_translations_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE app_menus (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', internal_name VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', enabled TINYINT(1) DEFAULT 1 NOT NULL, UNIQUE INDEX UNIQ_INTERNAL_NAME (internal_name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE app_menu_translations ADD CONSTRAINT FK_18F086BD2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES app_menus (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE app_menu_translations DROP FOREIGN KEY FK_18F086BD2C2AC5D3');
        $this->addSql('DROP TABLE app_menu_translations');
        $this->addSql('DROP TABLE app_menus');
    }
}
