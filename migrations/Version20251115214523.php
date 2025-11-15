<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251115214523 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE exemplaire DROP CONSTRAINT fk_5ef83c92b83297e7');
        $this->addSql('DROP INDEX idx_5ef83c92b83297e7');
        $this->addSql('ALTER TABLE exemplaire DROP reservation_id');
        $this->addSql('DROP INDEX uniq_42c84955f0840037');
        $this->addSql('ALTER TABLE reservation ADD exemplaire_id INT NOT NULL');
        $this->addSql('ALTER TABLE reservation ADD date_retour_prevu TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE reservation ADD date_retour_reel TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849555843AA21 FOREIGN KEY (exemplaire_id) REFERENCES exemplaire (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_42C84955F0840037 ON reservation (emprunteur_id)');
        $this->addSql('CREATE INDEX IDX_42C849555843AA21 ON reservation (exemplaire_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE exemplaire ADD reservation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE exemplaire ADD CONSTRAINT fk_5ef83c92b83297e7 FOREIGN KEY (reservation_id) REFERENCES reservation (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_5ef83c92b83297e7 ON exemplaire (reservation_id)');
        $this->addSql('ALTER TABLE reservation DROP CONSTRAINT FK_42C849555843AA21');
        $this->addSql('DROP INDEX IDX_42C84955F0840037');
        $this->addSql('DROP INDEX IDX_42C849555843AA21');
        $this->addSql('ALTER TABLE reservation DROP exemplaire_id');
        $this->addSql('ALTER TABLE reservation DROP date_retour_prevu');
        $this->addSql('ALTER TABLE reservation DROP date_retour_reel');
        $this->addSql('CREATE UNIQUE INDEX uniq_42c84955f0840037 ON reservation (emprunteur_id)');
    }
}
