<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200113164443 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE quiz_reminder_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE quiz_reminder (id INT NOT NULL, chat_id INT DEFAULT NULL, time VARCHAR(255) NOT NULL, status VARCHAR(255) DEFAULT \'enable\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_96F3B6051A9A7125 ON quiz_reminder (chat_id)');
        $this->addSql('ALTER TABLE quiz_reminder ADD CONSTRAINT FK_96F3B6051A9A7125 FOREIGN KEY (chat_id) REFERENCES chats (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE accounts ADD time_zone VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE quiz_reminder_id_seq CASCADE');
        $this->addSql('DROP TABLE quiz_reminder');
        $this->addSql('ALTER TABLE accounts DROP time_zone');
    }
}
