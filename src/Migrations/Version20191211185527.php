<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191211185527 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE word_list');
        $this->addSql('ALTER TABLE directions ADD is_savable BOOLEAN DEFAULT \'false\' NOT NULL');
        $this->addSql('UPDATE directions SET is_savable = true WHERE id = 1');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE word_list (id INT NOT NULL, chat_id INT NOT NULL, word_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_4c5dfbf5e357438d ON word_list (word_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_4c5dfbf51a9a7125 ON word_list (chat_id)');
        $this->addSql('ALTER TABLE word_list ADD CONSTRAINT fk_4c5dfbf51a9a7125 FOREIGN KEY (chat_id) REFERENCES chats (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE word_list ADD CONSTRAINT fk_4c5dfbf5e357438d FOREIGN KEY (word_id) REFERENCES words (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE directions DROP is_savable');
        $this->addSql('DROP INDEX UNIQ_C85173F2821AB4F');
    }
}
