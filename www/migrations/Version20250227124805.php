<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250227124805 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE projet (projetid INT AUTO_INCREMENT NOT NULL, clientid INT NOT NULL, referentid INT NOT NULL, nom VARCHAR(255) NOT NULL, budgetinitial NUMERIC(15, 2) NOT NULL, seuilalerte NUMERIC(15, 2) NOT NULL, INDEX IDX_50159CA97F98CD1C (clientid), INDEX IDX_50159CA9A91152A2 (referentid), PRIMARY KEY(projetid)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE projet ADD CONSTRAINT FK_50159CA97F98CD1C FOREIGN KEY (clientid) REFERENCES client (clientid)');
        $this->addSql('ALTER TABLE projet ADD CONSTRAINT FK_50159CA9A91152A2 FOREIGN KEY (referentid) REFERENCES utilisateur (utilisateurid)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE projet DROP FOREIGN KEY FK_50159CA97F98CD1C');
        $this->addSql('ALTER TABLE projet DROP FOREIGN KEY FK_50159CA9A91152A2');
        $this->addSql('DROP TABLE projet');
    }
}
