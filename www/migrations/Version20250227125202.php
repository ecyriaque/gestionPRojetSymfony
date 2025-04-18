<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250227125202 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE alerte_budget (alerteid INT AUTO_INCREMENT NOT NULL, projetid INT NOT NULL, montantdepasse NUMERIC(15, 2) NOT NULL, datealerte DATE NOT NULL, INDEX IDX_9FF88F3945EF8A (projetid), PRIMARY KEY(alerteid)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE alerte_budget ADD CONSTRAINT FK_9FF88F3945EF8A FOREIGN KEY (projetid) REFERENCES projet (projetid)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE alerte_budget DROP FOREIGN KEY FK_9FF88F3945EF8A');
        $this->addSql('DROP TABLE alerte_budget');
    }
}
