<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190821195858 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create word list table';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE word_lists_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE word_lists (id INT NOT NULL, chat_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D69594631A9A7125 ON word_lists (chat_id)');
        $this->addSql('CREATE TABLE lists2words (word_list_id INT NOT NULL, word_id INT NOT NULL, PRIMARY KEY(word_list_id, word_id))');
        $this->addSql('CREATE INDEX IDX_DEE27F5237DC6178 ON lists2words (word_list_id)');
        $this->addSql('CREATE INDEX IDX_DEE27F52E357438D ON lists2words (word_id)');
        $this->addSql('ALTER TABLE word_lists ADD CONSTRAINT FK_D69594631A9A7125 FOREIGN KEY (chat_id) REFERENCES chats (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE lists2words ADD CONSTRAINT FK_DEE27F5237DC6178 FOREIGN KEY (word_list_id) REFERENCES word_lists (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE lists2words ADD CONSTRAINT FK_DEE27F52E357438D FOREIGN KEY (word_id) REFERENCES words (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE lists2words DROP CONSTRAINT FK_DEE27F5237DC6178');
        $this->addSql('DROP SEQUENCE word_lists_id_seq CASCADE');
        $this->addSql('DROP TABLE word_lists');
        $this->addSql('DROP TABLE lists2words');
    }
}
