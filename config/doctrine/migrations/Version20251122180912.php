<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251122180912 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE app_frequently_asked_question_translations (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', translatable_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', question VARCHAR(255) NOT NULL, answer LONGTEXT NOT NULL, locale VARCHAR(5) NOT NULL, INDEX IDX_ADAFD0882C2AC5D3 (translatable_id), UNIQUE INDEX app_frequently_asked_question_translations_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE app_frequently_asked_questions (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', position INT UNSIGNED DEFAULT 0 NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', enabled TINYINT(1) DEFAULT 1 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE app_frequently_asked_question_translations ADD CONSTRAINT FK_ADAFD0882C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES app_frequently_asked_questions (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE app_frequently_asked_question_translations DROP FOREIGN KEY FK_ADAFD0882C2AC5D3');
        $this->addSql('DROP TABLE app_frequently_asked_question_translations');
        $this->addSql('DROP TABLE app_frequently_asked_questions');
    }
}
