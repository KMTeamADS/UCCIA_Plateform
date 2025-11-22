<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251122171925 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE app_page_translations (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', translatable_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, content LONGTEXT DEFAULT NULL, locale VARCHAR(5) NOT NULL, INDEX IDX_D9EEDB562C2AC5D3 (translatable_id), UNIQUE INDEX UNIQ_LOCALE_SLUG (locale, slug), UNIQUE INDEX app_page_translations_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE app_pages (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', parent_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', type VARCHAR(50) DEFAULT \'standard\' NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_F6A768B8727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE app_page_translations ADD CONSTRAINT FK_D9EEDB562C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES app_pages (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE app_pages ADD CONSTRAINT FK_F6A768B8727ACA70 FOREIGN KEY (parent_id) REFERENCES app_pages (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE app_page_translations DROP FOREIGN KEY FK_D9EEDB562C2AC5D3');
        $this->addSql('ALTER TABLE app_pages DROP FOREIGN KEY FK_F6A768B8727ACA70');
        $this->addSql('DROP TABLE app_page_translations');
        $this->addSql('DROP TABLE app_pages');
    }
}
