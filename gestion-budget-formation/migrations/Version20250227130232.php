<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250227130232 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE email_list (id INT AUTO_INCREMENT NOT NULL, listeid INT NOT NULL, email VARCHAR(255) NOT NULL, INDEX IDX_DBFED89D145276CD (listeid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE liste_diffusion (listeid INT AUTO_INCREMENT NOT NULL, projetid INT NOT NULL, INDEX IDX_44C87023945EF8A (projetid), PRIMARY KEY(listeid)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE email_list ADD CONSTRAINT FK_DBFED89D145276CD FOREIGN KEY (listeid) REFERENCES liste_diffusion (listeid)');
        $this->addSql('ALTER TABLE liste_diffusion ADD CONSTRAINT FK_44C87023945EF8A FOREIGN KEY (projetid) REFERENCES projet (projetid)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE email_list DROP FOREIGN KEY FK_DBFED89D145276CD');
        $this->addSql('ALTER TABLE liste_diffusion DROP FOREIGN KEY FK_44C87023945EF8A');
        $this->addSql('DROP TABLE email_list');
        $this->addSql('DROP TABLE liste_diffusion');
    }
}
