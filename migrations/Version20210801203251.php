<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210801203251 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create product table.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'CREATE TABLE product (' .
            '  id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,' .
            '  name VARCHAR(255) NOT NULL,' .
            '  price INTEGER NOT NULL' .
            ')'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS product');
    }
}
