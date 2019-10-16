<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191016195545 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add queries for work with quizes';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE quiz_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE quiz_steps_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE quiz (id INT NOT NULL, chat_id INT DEFAULT NULL, length INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A412FA921A9A7125 ON quiz (chat_id)');
        $this->addSql('CREATE TABLE quiz_steps (id INT NOT NULL, quiz_step_id INT DEFAULT NULL, correct_word_id INT DEFAULT NULL, is_answered BOOLEAN NOT NULL, is_correct BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C85173F261E8B33C ON quiz_steps (quiz_step_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C85173F2821AB4F ON quiz_steps (correct_word_id)');
        $this->addSql('CREATE TABLE quiz_step2wrong_words (quiz_step_id INT NOT NULL, word_id INT NOT NULL, PRIMARY KEY(quiz_step_id, word_id))');
        $this->addSql('CREATE INDEX IDX_A7726C0361E8B33C ON quiz_step2wrong_words (quiz_step_id)');
        $this->addSql('CREATE INDEX IDX_A7726C03E357438D ON quiz_step2wrong_words (word_id)');
        $this->addSql('ALTER TABLE quiz ADD CONSTRAINT FK_A412FA921A9A7125 FOREIGN KEY (chat_id) REFERENCES chats (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quiz_steps ADD CONSTRAINT FK_C85173F261E8B33C FOREIGN KEY (quiz_step_id) REFERENCES quiz (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quiz_steps ADD CONSTRAINT FK_C85173F2821AB4F FOREIGN KEY (correct_word_id) REFERENCES words (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quiz_step2wrong_words ADD CONSTRAINT FK_A7726C0361E8B33C FOREIGN KEY (quiz_step_id) REFERENCES quiz_steps (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quiz_step2wrong_words ADD CONSTRAINT FK_A7726C03E357438D FOREIGN KEY (word_id) REFERENCES words (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE quiz_steps DROP CONSTRAINT FK_C85173F261E8B33C');
        $this->addSql('ALTER TABLE quiz_step2wrong_words DROP CONSTRAINT FK_A7726C0361E8B33C');
        $this->addSql('DROP SEQUENCE quiz_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE quiz_steps_id_seq CASCADE');
        $this->addSql('DROP TABLE quiz');
        $this->addSql('DROP TABLE quiz_steps');
        $this->addSql('DROP TABLE quiz_step2wrong_words');
    }
}
