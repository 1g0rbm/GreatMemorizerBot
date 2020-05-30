<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200519192848 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE quiz_steps ADD answer_word_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE quiz_steps ADD CONSTRAINT FK_C85173F2AB178CFC FOREIGN KEY (answer_word_id) REFERENCES words (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_C85173F2AB178CFC ON quiz_steps (answer_word_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE quiz_steps DROP CONSTRAINT FK_C85173F2AB178CFC');
        $this->addSql('DROP INDEX IDX_C85173F2AB178CFC');
        $this->addSql('ALTER TABLE quiz_steps DROP answer_word_id');
    }
}
