<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250608072331 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE company (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE consigne (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE delivery_location (id INT AUTO_INCREMENT NOT NULL, place VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE loading_locations (id INT AUTO_INCREMENT NOT NULL, place VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE shipment (id INT AUTO_INCREMENT NOT NULL, company_id INT DEFAULT NULL, consigne_id INT DEFAULT NULL, delivery_location_id INT DEFAULT NULL, loading_location_id INT DEFAULT NULL, transporteur_id INT DEFAULT NULL, type_loading_id INT DEFAULT NULL, creator_id INT DEFAULT NULL, seal_number VARCHAR(255) NOT NULL, quantity INT NOT NULL, arrival_time TIME NOT NULL COMMENT '(DC2Type:time_immutable)', departure_time TIME NOT NULL COMMENT '(DC2Type:time_immutable)', trailer_plate VARCHAR(255) NOT NULL, tractor_plate VARCHAR(255) NOT NULL, number_reference INT NOT NULL, nombre_palette INT NOT NULL, plomb1 VARCHAR(255) NOT NULL, tract1 VARCHAR(255) NOT NULL, quantite2 INT NOT NULL, created_at DATE NOT NULL COMMENT '(DC2Type:date_immutable)', INDEX IDX_2CB20DC979B1AD6 (company_id), INDEX IDX_2CB20DC8C063686 (consigne_id), INDEX IDX_2CB20DC3A5080C8 (delivery_location_id), INDEX IDX_2CB20DC7A65737D (loading_location_id), INDEX IDX_2CB20DC97C86FA4 (transporteur_id), INDEX IDX_2CB20DCE338982D (type_loading_id), INDEX IDX_2CB20DC61220EA6 (creator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE transporteur (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE type_loading (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, is_verify TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_1483A5E9F85E0677 (username), UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', available_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', delivered_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE shipment ADD CONSTRAINT FK_2CB20DC979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE shipment ADD CONSTRAINT FK_2CB20DC8C063686 FOREIGN KEY (consigne_id) REFERENCES consigne (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE shipment ADD CONSTRAINT FK_2CB20DC3A5080C8 FOREIGN KEY (delivery_location_id) REFERENCES delivery_location (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE shipment ADD CONSTRAINT FK_2CB20DC7A65737D FOREIGN KEY (loading_location_id) REFERENCES loading_locations (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE shipment ADD CONSTRAINT FK_2CB20DC97C86FA4 FOREIGN KEY (transporteur_id) REFERENCES transporteur (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE shipment ADD CONSTRAINT FK_2CB20DCE338982D FOREIGN KEY (type_loading_id) REFERENCES type_loading (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE shipment ADD CONSTRAINT FK_2CB20DC61220EA6 FOREIGN KEY (creator_id) REFERENCES users (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE shipment DROP FOREIGN KEY FK_2CB20DC979B1AD6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE shipment DROP FOREIGN KEY FK_2CB20DC8C063686
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE shipment DROP FOREIGN KEY FK_2CB20DC3A5080C8
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE shipment DROP FOREIGN KEY FK_2CB20DC7A65737D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE shipment DROP FOREIGN KEY FK_2CB20DC97C86FA4
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE shipment DROP FOREIGN KEY FK_2CB20DCE338982D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE shipment DROP FOREIGN KEY FK_2CB20DC61220EA6
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE company
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE consigne
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE delivery_location
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE loading_locations
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE shipment
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE transporteur
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE type_loading
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE users
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE messenger_messages
        SQL);
    }
}
