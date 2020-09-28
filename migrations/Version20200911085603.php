<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200911085603 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product_feature DROP FOREIGN KEY FK_8F95F95460E4B879');
        $this->addSql('DROP TABLE feature');
        $this->addSql('DROP TABLE product_feature');
        $this->addSql('ALTER TABLE product ADD length DOUBLE PRECISION NOT NULL, ADD width DOUBLE PRECISION NOT NULL, ADD height DOUBLE PRECISION NOT NULL, ADD wifi TINYINT(1) DEFAULT NULL, ADD video4k TINYINT(1) DEFAULT NULL, ADD bluetooth TINYINT(1) DEFAULT NULL, ADD lte4_g TINYINT(1) DEFAULT NULL, ADD camera TINYINT(1) DEFAULT NULL, ADD nfc TINYINT(1) DEFAULT NULL, DROP dimensions, CHANGE updated_at updated_at DATETIME DEFAULT NULL, CHANGE screen screen DOUBLE PRECISION NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE feature (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE product_feature (product_id INT NOT NULL, feature_id INT NOT NULL, INDEX IDX_CE0E6ED660E4B879 (feature_id), INDEX IDX_CE0E6ED64584665A (product_id), PRIMARY KEY(product_id, feature_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE product_feature ADD CONSTRAINT FK_8F95F9543B7323CB FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_feature ADD CONSTRAINT FK_8F95F95460E4B879 FOREIGN KEY (feature_id) REFERENCES feature (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product ADD dimensions VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP length, DROP width, DROP height, DROP wifi, DROP video4k, DROP bluetooth, DROP lte4_g, DROP camera, DROP nfc, CHANGE updated_at updated_at DATETIME NOT NULL, CHANGE screen screen VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
