<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230511131302 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE image_save (id INT AUTO_INCREMENT NOT NULL, workspace_id INT DEFAULT NULL, image_name VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_A75CD04082D40A1F (workspace_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE image_save ADD CONSTRAINT FK_A75CD04082D40A1F FOREIGN KEY (workspace_id) REFERENCES workspace (id)');
        $this->addSql('ALTER TABLE workspace DROP image_name, DROP updated_at');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE image_save DROP FOREIGN KEY FK_A75CD04082D40A1F');
        $this->addSql('DROP TABLE image_save');
        $this->addSql('ALTER TABLE workspace ADD image_name VARCHAR(255) DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }
}
