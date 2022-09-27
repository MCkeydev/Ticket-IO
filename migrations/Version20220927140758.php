<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220927140758 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ticket_technicien (ticket_id INT NOT NULL, technicien_id INT NOT NULL, INDEX IDX_BBBE18700047D2 (ticket_id), INDEX IDX_BBBE1813457256 (technicien_id), PRIMARY KEY(ticket_id, technicien_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ticket_technicien ADD CONSTRAINT FK_BBBE18700047D2 FOREIGN KEY (ticket_id) REFERENCES ticket (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ticket_technicien ADD CONSTRAINT FK_BBBE1813457256 FOREIGN KEY (technicien_id) REFERENCES technicien (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ticket_technicien DROP FOREIGN KEY FK_BBBE18700047D2');
        $this->addSql('ALTER TABLE ticket_technicien DROP FOREIGN KEY FK_BBBE1813457256');
        $this->addSql('DROP TABLE ticket_technicien');
    }
}
