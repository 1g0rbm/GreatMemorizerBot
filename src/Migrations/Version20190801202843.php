<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190801202843 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add word_list table';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE word_list (id INT NOT NULL, chat_id INT NOT NULL, word_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4C5DFBF51A9A7125 ON word_list (chat_id)');
        $this->addSql('CREATE INDEX IDX_4C5DFBF5E357438D ON word_list (word_id)');
        $this->addSql('ALTER TABLE word_list ADD CONSTRAINT FK_4C5DFBF51A9A7125 FOREIGN KEY (chat_id) REFERENCES chats (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE word_list ADD CONSTRAINT FK_4C5DFBF5E357438D FOREIGN KEY (word_id) REFERENCES words (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE word_list');
    }
}
