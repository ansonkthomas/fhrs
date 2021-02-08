<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210205190436 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE establishment ADD authority_id INT NOT NULL');
        $this->addSql('ALTER TABLE establishment ADD CONSTRAINT FK_DBEFB1EE81EC865B FOREIGN KEY (authority_id) REFERENCES authority (id)');
        $this->addSql('CREATE INDEX IDX_DBEFB1EE81EC865B ON establishment (authority_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE establishment DROP FOREIGN KEY FK_DBEFB1EE81EC865B');
        $this->addSql('DROP INDEX IDX_DBEFB1EE81EC865B ON establishment');
        $this->addSql('ALTER TABLE establishment DROP authority_id');
    }
}
