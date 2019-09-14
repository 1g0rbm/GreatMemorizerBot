<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190913104716 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create tables for accounts and directions';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE directions_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE accounts_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE directions (id INT NOT NULL, lang_from VARCHAR(2) NOT NULL, lang_to VARCHAR(2) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX direction_idx ON directions (lang_from, lang_to)');
        $this->addSql('CREATE TABLE accounts (id INT NOT NULL, chat_id INT DEFAULT NULL, direction_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CAC89EAC1A9A7125 ON accounts (chat_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CAC89EACAF73D997 ON accounts (direction_id)');
        $this->addSql('ALTER TABLE accounts ADD CONSTRAINT FK_CAC89EAC1A9A7125 FOREIGN KEY (chat_id) REFERENCES chats (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE accounts ADD CONSTRAINT FK_CAC89EACAF73D997 FOREIGN KEY (direction_id) REFERENCES directions (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE users');

        $this->addSql('INSERT INTO directions (id, lang_from, lang_to) VALUES (1, \'en\', \'ru\')');
        $this->addSql('INSERT INTO directions (id, lang_from, lang_to) VALUES (2, \'ru\', \'en\')');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE accounts DROP CONSTRAINT FK_CAC89EACAF73D997');
        $this->addSql('DROP SEQUENCE directions_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE accounts_id_seq CASCADE');
        $this->addSql('CREATE TABLE users (id INT NOT NULL, telegram_id INT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, user_name VARCHAR(255) NOT NULL, PRIMARY KEY(id, telegram_id))');
        $this->addSql('DROP TABLE directions');
        $this->addSql('DROP TABLE accounts');
    }
}
