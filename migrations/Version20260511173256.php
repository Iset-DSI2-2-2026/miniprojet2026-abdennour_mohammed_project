<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260511173256 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE auteur (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, prenom VARCHAR(100) NOT NULL, biographie CLOB DEFAULT NULL, nationalite VARCHAR(50) NOT NULL)');
        $this->addSql('CREATE TABLE genre (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, description CLOB DEFAULT NULL, couleur VARCHAR(7) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_835033F86C6E55B5 ON genre (nom)');
        $this->addSql('CREATE TABLE livre (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, resume CLOB NOT NULL, isbn VARCHAR(13) NOT NULL, nb_pages INTEGER NOT NULL, date_publication DATE NOT NULL, disponible BOOLEAN NOT NULL, image_name VARCHAR(255) DEFAULT NULL, auteur_id INTEGER NOT NULL, genre_id INTEGER NOT NULL, ajoute_par_id INTEGER DEFAULT NULL, CONSTRAINT FK_AC634F9960BB6FE6 FOREIGN KEY (auteur_id) REFERENCES auteur (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_AC634F994296D31F FOREIGN KEY (genre_id) REFERENCES genre (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_AC634F99DAA76F43 FOREIGN KEY (ajoute_par_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_AC634F9960BB6FE6 ON livre (auteur_id)');
        $this->addSql('CREATE INDEX IDX_AC634F994296D31F ON livre (genre_id)');
        $this->addSql('CREATE INDEX IDX_AC634F99DAA76F43 ON livre (ajoute_par_id)');
        $this->addSql('CREATE TABLE livre_tag (livre_id INTEGER NOT NULL, tag_id INTEGER NOT NULL, PRIMARY KEY (livre_id, tag_id), CONSTRAINT FK_64DC1D0D37D925CB FOREIGN KEY (livre_id) REFERENCES livre (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_64DC1D0DBAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_64DC1D0D37D925CB ON livre_tag (livre_id)');
        $this->addSql('CREATE INDEX IDX_64DC1D0DBAD26311 ON livre_tag (tag_id)');
        $this->addSql('CREATE TABLE tag (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, couleur VARCHAR(7) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_389B7836C6E55B5 ON tag (nom)');
        $this->addSql('CREATE TABLE "user" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, pseudo VARCHAR(50) NOT NULL, roles CLOB NOT NULL, password VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON "user" (email)');
        $this->addSql('CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 ON messenger_messages (queue_name, available_at, delivered_at, id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE auteur');
        $this->addSql('DROP TABLE genre');
        $this->addSql('DROP TABLE livre');
        $this->addSql('DROP TABLE livre_tag');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
