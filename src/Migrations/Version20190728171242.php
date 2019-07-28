<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190728171242 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE words_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE words (id INT NOT NULL, lang_code VARCHAR(2) NOT NULL, text VARCHAR(255) NOT NULL, pos VARCHAR(100) NOT NULL, transcription VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX word_text ON words (text)');
        $this->addSql('CREATE TABLE words2translation (word_id INT NOT NULL, translation_id INT NOT NULL, PRIMARY KEY(word_id, translation_id))');
        $this->addSql('CREATE INDEX IDX_210B8E65E357438D ON words2translation (word_id)');
        $this->addSql('CREATE INDEX IDX_210B8E659CAA2B25 ON words2translation (translation_id)');
        $this->addSql('CREATE TABLE words2synonims (word_id INT NOT NULL, synonym_id INT NOT NULL, PRIMARY KEY(word_id, synonym_id))');
        $this->addSql('CREATE INDEX IDX_B54C8BF9E357438D ON words2synonims (word_id)');
        $this->addSql('CREATE INDEX IDX_B54C8BF98C1B728E ON words2synonims (synonym_id)');
        $this->addSql('CREATE TABLE chats (id INT NOT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, username VARCHAR(255) DEFAULT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE users (id INT NOT NULL, telegram_id INT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, user_name VARCHAR(255) NOT NULL, PRIMARY KEY(id, telegram_id))');
        $this->addSql('ALTER TABLE words2translation ADD CONSTRAINT FK_210B8E65E357438D FOREIGN KEY (word_id) REFERENCES words (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE words2translation ADD CONSTRAINT FK_210B8E659CAA2B25 FOREIGN KEY (translation_id) REFERENCES words (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE words2synonims ADD CONSTRAINT FK_B54C8BF9E357438D FOREIGN KEY (word_id) REFERENCES words (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE words2synonims ADD CONSTRAINT FK_B54C8BF98C1B728E FOREIGN KEY (synonym_id) REFERENCES words (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE words2translation DROP CONSTRAINT FK_210B8E65E357438D');
        $this->addSql('ALTER TABLE words2translation DROP CONSTRAINT FK_210B8E659CAA2B25');
        $this->addSql('ALTER TABLE words2synonims DROP CONSTRAINT FK_B54C8BF9E357438D');
        $this->addSql('ALTER TABLE words2synonims DROP CONSTRAINT FK_B54C8BF98C1B728E');
        $this->addSql('DROP SEQUENCE words_id_seq CASCADE');
        $this->addSql('DROP TABLE words');
        $this->addSql('DROP TABLE words2translation');
        $this->addSql('DROP TABLE words2synonims');
        $this->addSql('DROP TABLE chats');
        $this->addSql('DROP TABLE users');
    }
}
