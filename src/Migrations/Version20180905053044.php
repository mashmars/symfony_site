<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180905053044 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE album (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, userid_id INT NOT NULL, title VARCHAR(255) NOT NULL, views INT DEFAULT NULL, collect_count INT DEFAULT NULL, comment_count INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, descript VARCHAR(255) DEFAULT NULL, images LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', thumb VARCHAR(255) DEFAULT NULL, INDEX IDX_39986E4312469DE2 (category_id), INDEX IDX_39986E4358E0A285 (userid_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE album_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, created_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE album_collect (id INT AUTO_INCREMENT NOT NULL, userid_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, INDEX IDX_F1D13FE658E0A285 (userid_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE album_comment (id INT AUTO_INCREMENT NOT NULL, userid_id INT DEFAULT NULL, album_id INT DEFAULT NULL, content VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL, INDEX IDX_C1A30F7E58E0A285 (userid_id), INDEX IDX_C1A30F7E1137ABCF (album_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE album ADD CONSTRAINT FK_39986E4312469DE2 FOREIGN KEY (category_id) REFERENCES album_category (id)');
        $this->addSql('ALTER TABLE album ADD CONSTRAINT FK_39986E4358E0A285 FOREIGN KEY (userid_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE album_collect ADD CONSTRAINT FK_F1D13FE658E0A285 FOREIGN KEY (userid_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE album_comment ADD CONSTRAINT FK_C1A30F7E58E0A285 FOREIGN KEY (userid_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE album_comment ADD CONSTRAINT FK_C1A30F7E1137ABCF FOREIGN KEY (album_id) REFERENCES album (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE album_comment DROP FOREIGN KEY FK_C1A30F7E1137ABCF');
        $this->addSql('ALTER TABLE album DROP FOREIGN KEY FK_39986E4312469DE2');
        $this->addSql('DROP TABLE album');
        $this->addSql('DROP TABLE album_category');
        $this->addSql('DROP TABLE album_collect');
        $this->addSql('DROP TABLE album_comment');
    }
}
