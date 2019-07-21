<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190721091242 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create users and chats tables';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'postgresql',
            'Migration can only be executed safely on \'postgresql\'.'
        );

        $this->addSql(
            'CREATE TABLE chats 
                                (
                                    id INT NOT NULL,
                                    first_name VARCHAR(255) NOT NULL,
                                    last_name VARCHAR(255) NOT NULL,
                                    username VARCHAR(255) NOT NULL,
                                    type VARCHAR(255) NOT NULL,
                                    PRIMARY KEY(id)
                                )'
        );
        $this->addSql(
            'CREATE TABLE users 
                                (
                                    id INT NOT NULL,
                                    telegram_id INT NOT NULL,
                                    first_name VARCHAR(255) NOT NULL,
                                    last_name VARCHAR(255) NOT NULL,
                                    user_name VARCHAR(255) NOT NULL,
                                    PRIMARY KEY(id, telegram_id)
                                )'
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'postgresql',
            'Migration can only be executed safely on \'postgresql\'.'
        );

        $this->addSql('DROP TABLE chats');
        $this->addSql('DROP TABLE users');
    }
}
