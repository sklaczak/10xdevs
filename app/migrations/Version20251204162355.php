<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251204162355 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invoice ALTER net_amount DROP NOT NULL');
        $this->addSql('ALTER TABLE invoice ALTER gross_amount DROP NOT NULL');
        $this->addSql('ALTER TABLE invoice ALTER tax_amount DROP NOT NULL');
        $this->addSql('ALTER TABLE invoice ALTER tax_rate DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invoice ALTER net_amount SET NOT NULL');
        $this->addSql('ALTER TABLE invoice ALTER gross_amount SET NOT NULL');
        $this->addSql('ALTER TABLE invoice ALTER tax_amount SET NOT NULL');
        $this->addSql('ALTER TABLE invoice ALTER tax_rate SET NOT NULL');
    }
}
