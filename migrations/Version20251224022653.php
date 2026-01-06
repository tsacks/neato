<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251224022653 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(60) NOT NULL, slug VARCHAR(60) NOT NULL, parent_id INT DEFAULT NULL, redirect_id INT DEFAULT NULL, INDEX IDX_64C19C1727ACA70 (parent_id), UNIQUE INDEX UNIQ_64C19C1B42D874D (redirect_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE link (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(60) NOT NULL, url VARCHAR(255) NOT NULL, city VARCHAR(60) DEFAULT NULL, state VARCHAR(20) DEFAULT NULL, country VARCHAR(15) DEFAULT NULL, name VARCHAR(40) DEFAULT NULL, email VARCHAR(40) DEFAULT NULL, contact TINYINT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE link_category (link_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_CBE67908ADA40271 (link_id), INDEX IDX_CBE6790812469DE2 (category_id), PRIMARY KEY (link_id, category_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1727ACA70 FOREIGN KEY (parent_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1B42D874D FOREIGN KEY (redirect_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE link_category ADD CONSTRAINT FK_CBE67908ADA40271 FOREIGN KEY (link_id) REFERENCES link (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE link_category ADD CONSTRAINT FK_CBE6790812469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1727ACA70');
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1B42D874D');
        $this->addSql('ALTER TABLE link_category DROP FOREIGN KEY FK_CBE67908ADA40271');
        $this->addSql('ALTER TABLE link_category DROP FOREIGN KEY FK_CBE6790812469DE2');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE link');
        $this->addSql('DROP TABLE link_category');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
