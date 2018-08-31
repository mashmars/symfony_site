<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180828024743 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, userid_id INT NOT NULL, name VARCHAR(100) NOT NULL, created_at DATETIME DEFAULT NULL, INDEX IDX_64C19C158E0A285 (userid_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post (id INT AUTO_INCREMENT NOT NULL, userid_id INT NOT NULL, title VARCHAR(255) NOT NULL, descript VARCHAR(255) DEFAULT NULL, thumb VARCHAR(255) DEFAULT NULL, status TINYINT(1) DEFAULT NULL, is_comment TINYINT(1) DEFAULT NULL, views INT DEFAULT NULL, comments INT DEFAULT NULL, content LONGTEXT DEFAULT NULL, INDEX IDX_5A8A6C8D58E0A285 (userid_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, userid_id INT NOT NULL, name VARCHAR(100) NOT NULL, created_at DATETIME DEFAULT NULL, INDEX IDX_389B78358E0A285 (userid_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C158E0A285 FOREIGN KEY (userid_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D58E0A285 FOREIGN KEY (userid_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE tag ADD CONSTRAINT FK_389B78358E0A285 FOREIGN KEY (userid_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE tag');
    }
}
