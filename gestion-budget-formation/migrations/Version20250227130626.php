<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250227130626 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE formation (formationid INT AUTO_INCREMENT NOT NULL, sessionid INT NOT NULL, organisme VARCHAR(255) NOT NULL, couht NUMERIC(15, 2) NOT NULL, tauxtva NUMERIC(5, 2) NOT NULL, dateformation DATE NOT NULL, INDEX IDX_404021BFACD49154 (sessionid), PRIMARY KEY(formationid)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE session_formation (sessionid INT AUTO_INCREMENT NOT NULL, projetid INT NOT NULL, datedebut DATE NOT NULL, datefin DATE NOT NULL, couttotal NUMERIC(15, 2) NOT NULL, INDEX IDX_3A264B53945EF8A (projetid), PRIMARY KEY(sessionid)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE formation ADD CONSTRAINT FK_404021BFACD49154 FOREIGN KEY (sessionid) REFERENCES session_formation (sessionid)');
        $this->addSql('ALTER TABLE session_formation ADD CONSTRAINT FK_3A264B53945EF8A FOREIGN KEY (projetid) REFERENCES projet (projetid)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE formation DROP FOREIGN KEY FK_404021BFACD49154');
        $this->addSql('ALTER TABLE session_formation DROP FOREIGN KEY FK_3A264B53945EF8A');
        $this->addSql('DROP TABLE formation');
        $this->addSql('DROP TABLE session_formation');
    }
}
