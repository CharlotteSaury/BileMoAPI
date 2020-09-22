<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200922130128 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE client_customer (client_id INT NOT NULL, customer_id INT NOT NULL, INDEX IDX_AFDFBECC19EB6921 (client_id), INDEX IDX_AFDFBECC9395C3F3 (customer_id), PRIMARY KEY(client_id, customer_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE client_customer ADD CONSTRAINT FK_AFDFBECC19EB6921 FOREIGN KEY (client_id) REFERENCES client (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE client_customer ADD CONSTRAINT FK_AFDFBECC9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE customer DROP FOREIGN KEY FK_81398E0919EB6921');
        $this->addSql('DROP INDEX IDX_81398E0919EB6921 ON customer');
        $this->addSql('ALTER TABLE customer DROP client_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE client_customer');
        $this->addSql('ALTER TABLE customer ADD client_id INT NOT NULL');
        $this->addSql('ALTER TABLE customer ADD CONSTRAINT FK_81398E0919EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('CREATE INDEX IDX_81398E0919EB6921 ON customer (client_id)');
    }
}
