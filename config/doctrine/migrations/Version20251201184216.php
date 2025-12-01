<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251201184216 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE app_posts ADD image VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:media)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE app_posts DROP image');
    }
}
