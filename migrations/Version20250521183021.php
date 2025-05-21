<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250521183021 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial migration for Product and Coupon tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE coupon (
                id SERIAL NOT NULL,
                code VARCHAR(50) NOT NULL,
                type VARCHAR(10) NOT NULL,
                value DOUBLE PRECISION NOT NULL,
                PRIMARY KEY(id)
            )
        SQL);

        $this->addSql('CREATE UNIQUE INDEX UNIQ_coupon_code ON coupon (code)');

        $this->addSql(<<<'SQL'
            CREATE TABLE product (
                id SERIAL NOT NULL,
                name VARCHAR(255) NOT NULL,
                price DOUBLE PRECISION NOT NULL,
                PRIMARY KEY(id)
            )
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS coupon');
        $this->addSql('DROP TABLE IF EXISTS product');
    }
}
