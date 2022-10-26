<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221018072700 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commentaire (id INT AUTO_INCREMENT NOT NULL, auteur_id INT NOT NULL, ticket_id INT NOT NULL, commentaire VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_67F068BC60BB6FE6 (auteur_id), INDEX IDX_67F068BC700047D2 (ticket_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE solution (id INT AUTO_INCREMENT NOT NULL, auteur_id INT NOT NULL, ticket_id INT NOT NULL, solution VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_9F3329DB60BB6FE6 (auteur_id), UNIQUE INDEX UNIQ_9F3329DB700047D2 (ticket_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC60BB6FE6 FOREIGN KEY (auteur_id) REFERENCES technicien (id)');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC700047D2 FOREIGN KEY (ticket_id) REFERENCES ticket (id)');
        $this->addSql('ALTER TABLE solution ADD CONSTRAINT FK_9F3329DB60BB6FE6 FOREIGN KEY (auteur_id) REFERENCES technicien (id)');
        $this->addSql('ALTER TABLE solution ADD CONSTRAINT FK_9F3329DB700047D2 FOREIGN KEY (ticket_id) REFERENCES ticket (id)');
        $this->addSql('ALTER TABLE tache ADD ticket_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tache ADD CONSTRAINT FK_93872075700047D2 FOREIGN KEY (ticket_id) REFERENCES ticket (id)');
        $this->addSql('CREATE INDEX IDX_93872075700047D2 ON tache (ticket_id)');
        $this->addSql('ALTER TABLE ticket ADD status_id INT NOT NULL, ADD criticite_id INT NOT NULL, ADD gravite_id INT NOT NULL');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA36BF700BD FOREIGN KEY (status_id) REFERENCES status (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3C141C0A0 FOREIGN KEY (criticite_id) REFERENCES criticite (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA32B4CDA96 FOREIGN KEY (gravite_id) REFERENCES gravite (id)');
        $this->addSql('CREATE INDEX IDX_97A0ADA36BF700BD ON ticket (status_id)');
        $this->addSql('CREATE INDEX IDX_97A0ADA3C141C0A0 ON ticket (criticite_id)');
        $this->addSql('CREATE INDEX IDX_97A0ADA32B4CDA96 ON ticket (gravite_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC60BB6FE6');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC700047D2');
        $this->addSql('ALTER TABLE solution DROP FOREIGN KEY FK_9F3329DB60BB6FE6');
        $this->addSql('ALTER TABLE solution DROP FOREIGN KEY FK_9F3329DB700047D2');
        $this->addSql('DROP TABLE commentaire');
        $this->addSql('DROP TABLE solution');
        $this->addSql('ALTER TABLE tache DROP FOREIGN KEY FK_93872075700047D2');
        $this->addSql('DROP INDEX IDX_93872075700047D2 ON tache');
        $this->addSql('ALTER TABLE tache DROP ticket_id');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA36BF700BD');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA3C141C0A0');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA32B4CDA96');
        $this->addSql('DROP INDEX IDX_97A0ADA36BF700BD ON ticket');
        $this->addSql('DROP INDEX IDX_97A0ADA3C141C0A0 ON ticket');
        $this->addSql('DROP INDEX IDX_97A0ADA32B4CDA96 ON ticket');
        $this->addSql('ALTER TABLE ticket DROP status_id, DROP criticite_id, DROP gravite_id');
    }
}
