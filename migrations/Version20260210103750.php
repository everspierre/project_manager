<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260210103750 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE file (id INT AUTO_INCREMENT NOT NULL, filename VARCHAR(255) NOT NULL, size INT NOT NULL, owner_id INT NOT NULL, owner_type VARCHAR(50) NOT NULL, INDEX idx_file_owner (owner_id, owner_type), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE project (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, owner_id INT NOT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, INDEX IDX_2FB3D0EE7E3C61F9 (owner_id), INDEX IDX_2FB3D0EEDE12AB56 (created_by), INDEX IDX_2FB3D0EE16FE72E1 (updated_by), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE project_user (project_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_B4021E51166D1F9C (project_id), INDEX IDX_B4021E51A76ED395 (user_id), PRIMARY KEY (project_id, user_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE task (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, starting_date DATETIME NOT NULL, ending_date DATETIME DEFAULT NULL, state VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, project_id INT NOT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, INDEX IDX_527EDB25166D1F9C (project_id), INDEX IDX_527EDB25DE12AB56 (created_by), INDEX IDX_527EDB2516FE72E1 (updated_by), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, INDEX IDX_8D93D649DE12AB56 (created_by), INDEX IDX_8D93D64916FE72E1 (updated_by), UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EE7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EEDE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EE16FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE project_user ADD CONSTRAINT FK_B4021E51166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project_user ADD CONSTRAINT FK_B4021E51A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB2516FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64916FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EE7E3C61F9');
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EEDE12AB56');
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EE16FE72E1');
        $this->addSql('ALTER TABLE project_user DROP FOREIGN KEY FK_B4021E51166D1F9C');
        $this->addSql('ALTER TABLE project_user DROP FOREIGN KEY FK_B4021E51A76ED395');
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB25166D1F9C');
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB25DE12AB56');
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB2516FE72E1');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649DE12AB56');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64916FE72E1');
        $this->addSql('DROP TABLE file');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE project_user');
        $this->addSql('DROP TABLE task');
        $this->addSql('DROP TABLE user');
    }
}
