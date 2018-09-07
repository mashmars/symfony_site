<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180904020438 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE topic (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT DEFAULT NULL, views INT DEFAULT NULL, comment_count INT DEFAULT NULL, collect_count INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, status INT DEFAULT NULL, INDEX IDX_9D40DE1B12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE topic_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, created_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE topic_comment (id INT AUTO_INCREMENT NOT NULL, topic_id INT NOT NULL, userid_id INT NOT NULL, content VARCHAR(255) NOT NULL, like_count INT DEFAULT NULL, unlink_count INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, status SMALLINT DEFAULT NULL, INDEX IDX_1CDF0FB91F55203D (topic_id), INDEX IDX_1CDF0FB958E0A285 (userid_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE topic_comment_like (id INT AUTO_INCREMENT NOT NULL, comment_id INT DEFAULT NULL, userid_id INT DEFAULT NULL, type SMALLINT DEFAULT NULL, created_at DATETIME DEFAULT NULL, INDEX IDX_D45065CBF8697D13 (comment_id), INDEX IDX_D45065CB58E0A285 (userid_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE topic_like (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE topic ADD CONSTRAINT FK_9D40DE1B12469DE2 FOREIGN KEY (category_id) REFERENCES topic_category (id)');
        $this->addSql('ALTER TABLE topic_comment ADD CONSTRAINT FK_1CDF0FB91F55203D FOREIGN KEY (topic_id) REFERENCES topic (id)');
        $this->addSql('ALTER TABLE topic_comment ADD CONSTRAINT FK_1CDF0FB958E0A285 FOREIGN KEY (userid_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE topic_comment_like ADD CONSTRAINT FK_D45065CBF8697D13 FOREIGN KEY (comment_id) REFERENCES topic_comment (id)');
        $this->addSql('ALTER TABLE topic_comment_like ADD CONSTRAINT FK_D45065CB58E0A285 FOREIGN KEY (userid_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE topic_comment DROP FOREIGN KEY FK_1CDF0FB91F55203D');
        $this->addSql('ALTER TABLE topic DROP FOREIGN KEY FK_9D40DE1B12469DE2');
        $this->addSql('ALTER TABLE topic_comment_like DROP FOREIGN KEY FK_D45065CBF8697D13');
        $this->addSql('DROP TABLE topic');
        $this->addSql('DROP TABLE topic_category');
        $this->addSql('DROP TABLE topic_comment');
        $this->addSql('DROP TABLE topic_comment_like');
        $this->addSql('DROP TABLE topic_like');
    }
}
