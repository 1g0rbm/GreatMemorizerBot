<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200106111012 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE accounts ALTER chat_id SET NOT NULL');
        $this->addSql('ALTER TABLE quiz ADD word_list_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE quiz ADD CONSTRAINT FK_A412FA9237DC6178 FOREIGN KEY (word_list_id) REFERENCES word_lists (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_A412FA9237DC6178 ON quiz (word_list_id)');
        $this->addSql('ALTER TABLE quiz_steps ADD length INT DEFAULT 4 NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE accounts ALTER chat_id DROP NOT NULL');
        $this->addSql('ALTER TABLE quiz_steps DROP length');
        $this->addSql('ALTER TABLE quiz DROP CONSTRAINT FK_A412FA9237DC6178');
        $this->addSql('DROP INDEX IDX_A412FA9237DC6178');
        $this->addSql('ALTER TABLE quiz DROP word_list_id');
    }
}
