<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250227125624 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE facture (factureid INT AUTO_INCREMENT NOT NULL, projetid INT NOT NULL, dateemission DATE NOT NULL, montanttotal NUMERIC(15, 2) NOT NULL, etat VARCHAR(20) NOT NULL, INDEX IDX_FE8664103945EF8A (projetid), PRIMARY KEY(factureid)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE facture ADD CONSTRAINT FK_FE8664103945EF8A FOREIGN KEY (projetid) REFERENCES projet (projetid)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE facture DROP FOREIGN KEY FK_FE8664103945EF8A');
        $this->addSql('DROP TABLE facture');
    }
}
