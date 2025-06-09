<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250609193650 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE shipment DROP FOREIGN KEY FK_2CB20DC61220EA6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE shipment ADD CONSTRAINT FK_2CB20DC61220EA6 FOREIGN KEY (creator_id) REFERENCES users (id) ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE shipment DROP FOREIGN KEY FK_2CB20DC61220EA6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE shipment ADD CONSTRAINT FK_2CB20DC61220EA6 FOREIGN KEY (creator_id) REFERENCES users (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
    }
}
