<?php

namespace App\Tests;


use App\Entity\Reservation;
use App\Entity\Utilisateur;
use App\Entity\Exemplaire;
use PHPUnit\Framework\TestCase;

class ReservationTest extends TestCase
{
    public function testSettersAndGetters(): void
    {
        $reservation = new Reservation();
        $utilisateur = new Utilisateur();
        $exemplaire = new Exemplaire();

        $dateEmprunt = new \DateTime('2025-11-25');

        $reservation->setEmprunteur($utilisateur);
        $reservation->setExemplaire($exemplaire);
        $reservation->setDateEmprunt($dateEmprunt);

        $this->assertSame($utilisateur, $reservation->getEmprunteur());
        $this->assertSame($exemplaire, $reservation->getExemplaire());
        $this->assertSame($dateEmprunt, $reservation->getDateEmprunt());
    }

    public function testDateRetourPrevuAndReel(): void
    {
        $reservation = new Reservation();
        $dateRetourPrevu = new \DateTime('+7 days');
        $dateRetourReel = new \DateTime('+6 days');

        $reservation->setDateRetourPrevu($dateRetourPrevu);
        $reservation->setDateRetourReel($dateRetourReel);

        $this->assertSame($dateRetourPrevu, $reservation->getDateRetourPrevu());
        $this->assertSame($dateRetourReel, $reservation->getDateRetourReel());
    }
}
