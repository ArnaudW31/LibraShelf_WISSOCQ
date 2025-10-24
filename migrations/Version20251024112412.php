<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251024112412 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE auteur (id SERIAL NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, date_naissance DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN auteur.date_naissance IS \'(DC2Type:date_immutable)\'');
        $this->addSql('CREATE TABLE categorie (id SERIAL NOT NULL, nom VARCHAR(255) NOT NULL, duree_emprunt INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE categorie_ouvrage (categorie_id INT NOT NULL, ouvrage_id INT NOT NULL, PRIMARY KEY(categorie_id, ouvrage_id))');
        $this->addSql('CREATE INDEX IDX_D2B657ABCF5E72D ON categorie_ouvrage (categorie_id)');
        $this->addSql('CREATE INDEX IDX_D2B657A15D884B5 ON categorie_ouvrage (ouvrage_id)');
        $this->addSql('CREATE TABLE exemplaire (id SERIAL NOT NULL, reservation_id INT DEFAULT NULL, cote VARCHAR(255) DEFAULT NULL, etat INT NOT NULL, emplacement VARCHAR(255) NOT NULL, disponibilite BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5EF83C92B83297E7 ON exemplaire (reservation_id)');
        $this->addSql('CREATE TABLE ouvrage (id SERIAL NOT NULL, titre VARCHAR(255) NOT NULL, editeur VARCHAR(255) NOT NULL, isbn VARCHAR(13) NOT NULL, parution DATE NOT NULL, resume TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN ouvrage.parution IS \'(DC2Type:date_immutable)\'');
        $this->addSql('CREATE TABLE ouvrage_auteur (ouvrage_id INT NOT NULL, auteur_id INT NOT NULL, PRIMARY KEY(ouvrage_id, auteur_id))');
        $this->addSql('CREATE INDEX IDX_3E39E6E815D884B5 ON ouvrage_auteur (ouvrage_id)');
        $this->addSql('CREATE INDEX IDX_3E39E6E860BB6FE6 ON ouvrage_auteur (auteur_id)');
        $this->addSql('CREATE TABLE reservation (id SERIAL NOT NULL, emprunteur_id INT NOT NULL, date_emprunt TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_42C84955F0840037 ON reservation (emprunteur_id)');
        $this->addSql('CREATE TABLE tags (id SERIAL NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE tags_ouvrage (tags_id INT NOT NULL, ouvrage_id INT NOT NULL, PRIMARY KEY(tags_id, ouvrage_id))');
        $this->addSql('CREATE INDEX IDX_741420C98D7B4FB4 ON tags_ouvrage (tags_id)');
        $this->addSql('CREATE INDEX IDX_741420C915D884B5 ON tags_ouvrage (ouvrage_id)');
        $this->addSql('CREATE TABLE utilisateur (id SERIAL NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON utilisateur (email)');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE categorie_ouvrage ADD CONSTRAINT FK_D2B657ABCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE categorie_ouvrage ADD CONSTRAINT FK_D2B657A15D884B5 FOREIGN KEY (ouvrage_id) REFERENCES ouvrage (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE exemplaire ADD CONSTRAINT FK_5EF83C92B83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ouvrage_auteur ADD CONSTRAINT FK_3E39E6E815D884B5 FOREIGN KEY (ouvrage_id) REFERENCES ouvrage (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ouvrage_auteur ADD CONSTRAINT FK_3E39E6E860BB6FE6 FOREIGN KEY (auteur_id) REFERENCES auteur (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955F0840037 FOREIGN KEY (emprunteur_id) REFERENCES utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tags_ouvrage ADD CONSTRAINT FK_741420C98D7B4FB4 FOREIGN KEY (tags_id) REFERENCES tags (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tags_ouvrage ADD CONSTRAINT FK_741420C915D884B5 FOREIGN KEY (ouvrage_id) REFERENCES ouvrage (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE categorie_ouvrage DROP CONSTRAINT FK_D2B657ABCF5E72D');
        $this->addSql('ALTER TABLE categorie_ouvrage DROP CONSTRAINT FK_D2B657A15D884B5');
        $this->addSql('ALTER TABLE exemplaire DROP CONSTRAINT FK_5EF83C92B83297E7');
        $this->addSql('ALTER TABLE ouvrage_auteur DROP CONSTRAINT FK_3E39E6E815D884B5');
        $this->addSql('ALTER TABLE ouvrage_auteur DROP CONSTRAINT FK_3E39E6E860BB6FE6');
        $this->addSql('ALTER TABLE reservation DROP CONSTRAINT FK_42C84955F0840037');
        $this->addSql('ALTER TABLE tags_ouvrage DROP CONSTRAINT FK_741420C98D7B4FB4');
        $this->addSql('ALTER TABLE tags_ouvrage DROP CONSTRAINT FK_741420C915D884B5');
        $this->addSql('DROP TABLE auteur');
        $this->addSql('DROP TABLE categorie');
        $this->addSql('DROP TABLE categorie_ouvrage');
        $this->addSql('DROP TABLE exemplaire');
        $this->addSql('DROP TABLE ouvrage');
        $this->addSql('DROP TABLE ouvrage_auteur');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE tags');
        $this->addSql('DROP TABLE tags_ouvrage');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
