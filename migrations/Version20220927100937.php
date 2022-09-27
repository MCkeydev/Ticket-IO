<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220927100937 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ticket (id INT AUTO_INCREMENT NOT NULL, service_id INT NOT NULL, operateur_id INT NOT NULL, titre VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_97A0ADA3ED5CA9E6 (service_id), INDEX IDX_97A0ADA33F192FC (operateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA33F192FC FOREIGN KEY (operateur_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD service_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649ED5CA9E6 ON user (service_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA3ED5CA9E6');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA33F192FC');
        $this->addSql('DROP TABLE ticket');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649ED5CA9E6');
        $this->addSql('DROP INDEX IDX_8D93D649ED5CA9E6 ON user');
        $this->addSql('ALTER TABLE user DROP service_id');
    }
}
