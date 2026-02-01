<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251202135136 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invoice ADD person_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE invoice ALTER type DROP DEFAULT');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_90651744217BBB47 FOREIGN KEY (person_id) REFERENCES person (id) NOT DEFERRABLE');
        $this->addSql('CREATE INDEX IDX_90651744217BBB47 ON invoice (person_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invoice DROP CONSTRAINT FK_90651744217BBB47');
        $this->addSql('DROP INDEX IDX_90651744217BBB47');
        $this->addSql('ALTER TABLE invoice DROP person_id');
        $this->addSql('ALTER TABLE invoice ALTER type SET DEFAULT \'sale\'');
    }
}
