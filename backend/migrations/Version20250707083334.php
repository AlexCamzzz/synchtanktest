<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250707083334 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Creates the track table with MySQL compatible syntax';
    }

    public function up(Schema $schema): void
    {
        // MySQL syntax: INT AUTO_INCREMENT ... PRIMARY KEY(id) ... ENGINE = InnoDB
        $this->addSql('CREATE TABLE track (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, artist VARCHAR(255) NOT NULL, duration INT NOT NULL, isrc VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE track');
    }
}