<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230507151033 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category_workspace (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, price VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, reference VARCHAR(255) NOT NULL, INDEX IDX_F5299398A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subscription (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, price VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE workspace (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, category_workspace_id INT DEFAULT NULL, nbr_place INT NOT NULL, description LONGTEXT NOT NULL, title VARCHAR(255) NOT NULL, INDEX IDX_8D940019A76ED395 (user_id), INDEX IDX_8D9400195803298D (category_workspace_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE workspace ADD CONSTRAINT FK_8D940019A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE workspace ADD CONSTRAINT FK_8D9400195803298D FOREIGN KEY (category_workspace_id) REFERENCES category_workspace (id)');
        $this->addSql('ALTER TABLE user ADD subscription_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6499A1887DC FOREIGN KEY (subscription_id) REFERENCES subscription (id)');
        $this->addSql('CREATE INDEX IDX_8D93D6499A1887DC ON user (subscription_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6499A1887DC');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398A76ED395');
        $this->addSql('ALTER TABLE workspace DROP FOREIGN KEY FK_8D940019A76ED395');
        $this->addSql('ALTER TABLE workspace DROP FOREIGN KEY FK_8D9400195803298D');
        $this->addSql('DROP TABLE category_workspace');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE subscription');
        $this->addSql('DROP TABLE workspace');
        $this->addSql('DROP INDEX IDX_8D93D6499A1887DC ON user');
        $this->addSql('ALTER TABLE user DROP subscription_id');
    }
}
