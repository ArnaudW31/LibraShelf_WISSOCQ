<?php

namespace App\Tests;

use App\Entity\Exemplaire;
use App\Entity\Ouvrage;
use App\Enum\Etat;
use PHPUnit\Framework\TestCase;

class ExemplaireTest extends TestCase
{
    public function testSettersAndGetters(): void
    {
        $exemplaire = new Exemplaire();
        $exemplaire->setCote('C123');
        $exemplaire->setEtat(Etat::Bon);
        $exemplaire->setDisponibilite(true);

        $this->assertSame('C123', $exemplaire->getCote());
        $this->assertSame(Etat::Bon, $exemplaire->getEtat());
        $this->assertTrue($exemplaire->isDisponibilite());
    }

    public function testOuvrageAssociation(): void
    {
        $exemplaire = new Exemplaire();
        $ouvrage = new Ouvrage();

        $exemplaire->setOuvrage($ouvrage);
        $this->assertSame($ouvrage, $exemplaire->getOuvrage());
    }

    public function testDisponibilite(): void
    {
        $exemplaire = new Exemplaire();
        $exemplaire->setEtat(Etat::Bon);
        $exemplaire->setDisponibilite(true);

        $this->assertTrue($exemplaire->isDisponibilite());

        $exemplaire->setDisponibilite(false);
        $this->assertFalse($exemplaire->isDisponibilite());
    }
}
