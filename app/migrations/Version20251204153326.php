<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251204153326 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invoice ADD gross_amount NUMERIC(10, 2) NOT NULL');
        $this->addSql('ALTER TABLE invoice ADD tax_amount NUMERIC(10, 2) NOT NULL');
        $this->addSql('ALTER TABLE invoice ADD tax_rate NUMERIC(5, 2) NOT NULL');
        $this->addSql('ALTER TABLE invoice RENAME COLUMN amount TO net_amount');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invoice ADD amount NUMERIC(10, 2) NOT NULL');
        $this->addSql('ALTER TABLE invoice DROP net_amount');
        $this->addSql('ALTER TABLE invoice DROP gross_amount');
        $this->addSql('ALTER TABLE invoice DROP tax_amount');
        $this->addSql('ALTER TABLE invoice DROP tax_rate');
    }
}
