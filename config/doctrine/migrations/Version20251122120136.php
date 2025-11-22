<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251122120136 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE app_post_translations (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', translatable_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, content LONGTEXT DEFAULT NULL, locale VARCHAR(5) NOT NULL, INDEX IDX_CCCF0ACB2C2AC5D3 (translatable_id), UNIQUE INDEX UNIQ_LOCALE_SLUG (locale, slug), UNIQUE INDEX app_post_translations_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE app_posts (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', published_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE app_post_translations ADD CONSTRAINT FK_CCCF0ACB2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES app_posts (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE app_post_translations DROP FOREIGN KEY FK_CCCF0ACB2C2AC5D3');
        $this->addSql('DROP TABLE app_post_translations');
        $this->addSql('DROP TABLE app_posts');
    }
}
