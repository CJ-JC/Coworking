<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230521212903 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` ADD workspace_id INT DEFAULT NULL, ADD order_date DATETIME NOT NULL, ADD order_hour DATETIME NOT NULL');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F529939882D40A1F FOREIGN KEY (workspace_id) REFERENCES workspace (id)');
        $this->addSql('CREATE INDEX IDX_F529939882D40A1F ON `order` (workspace_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F529939882D40A1F');
        $this->addSql('DROP INDEX IDX_F529939882D40A1F ON `order`');
        $this->addSql('ALTER TABLE `order` DROP workspace_id, DROP order_date, DROP order_hour');
    }
}
