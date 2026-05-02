<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260421012959 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cart_item ADD size VARCHAR(20) DEFAULT \'Normale\'');
        $this->addSql('ALTER TABLE cart_item ADD instructions TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE order_item ADD size VARCHAR(20) DEFAULT NULL');
        $this->addSql('ALTER TABLE order_item ADD instructions TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD diet_tags JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD calories INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD proteins NUMERIC(5, 1) DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD carbs NUMERIC(5, 1) DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD fats NUMERIC(5, 1) DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD is_available BOOLEAN DEFAULT true NOT NULL');
        $this->addSql('ALTER TABLE product ADD is_bestseller BOOLEAN DEFAULT false NOT NULL');
        $this->addSql('ALTER TABLE product ADD sizes JSON DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cart_item DROP size');
        $this->addSql('ALTER TABLE cart_item DROP instructions');
        $this->addSql('ALTER TABLE order_item DROP size');
        $this->addSql('ALTER TABLE order_item DROP instructions');
        $this->addSql('ALTER TABLE product DROP diet_tags');
        $this->addSql('ALTER TABLE product DROP calories');
        $this->addSql('ALTER TABLE product DROP proteins');
        $this->addSql('ALTER TABLE product DROP carbs');
        $this->addSql('ALTER TABLE product DROP fats');
        $this->addSql('ALTER TABLE product DROP is_available');
        $this->addSql('ALTER TABLE product DROP is_bestseller');
        $this->addSql('ALTER TABLE product DROP sizes');
    }
}
