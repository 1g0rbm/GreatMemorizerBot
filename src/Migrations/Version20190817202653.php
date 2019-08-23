<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190817202653 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE word_list_id_seq CASCADE');
        $this->addSql('CREATE TABLE chats2words (chat_id INT NOT NULL, word_id INT NOT NULL, PRIMARY KEY(chat_id, word_id))');
        $this->addSql('CREATE INDEX IDX_20EE8A5D1A9A7125 ON chats2words (chat_id)');
        $this->addSql('CREATE INDEX IDX_20EE8A5DE357438D ON chats2words (word_id)');
        $this->addSql('ALTER TABLE chats2words ADD CONSTRAINT FK_20EE8A5D1A9A7125 FOREIGN KEY (chat_id) REFERENCES chats (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE chats2words ADD CONSTRAINT FK_20EE8A5DE357438D FOREIGN KEY (word_id) REFERENCES words (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE word_list_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('DROP TABLE chats2words');
    }
}

