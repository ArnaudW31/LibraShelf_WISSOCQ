<?php

namespace App\Tests;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Ouvrage;
use App\Entity\Exemplaire;
use App\Entity\Reservation;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;

class ReservationWorkflowTest extends WebTestCase
{
    public function testReservationFlow(): void
    {
        $client = static::createClient();
        $container = self::getContainer();

        /** @var EntityManagerInterface $em */
        $em = $container->get(EntityManagerInterface::class);

        // Création utilisateur
        $user = new Utilisateur();
        $user->setEmail('member@example.com');
        $user->setPassword('password');
        $user->setRoles(['ROLE_MEMBER']);
        $em->persist($user);

        // Création ouvrage et exemplaire
        $ouvrage = new Ouvrage();
        $ouvrage->setTitre('Symfony 6 Test');
        $em->persist($ouvrage);

        $exemplaire = new Exemplaire();
        $exemplaire->setOuvrage($ouvrage);
        $exemplaire->setDisponibilite(true);
        $em->persist($exemplaire);

        $em->flush();

        // Réserver l'exemplaire
        $reservation = new Reservation();
        $reservation->setEmprunteur($user);
        $reservation->setExemplaire($exemplaire);
        $reservation->setDateEmprunt(new \DateTime());
        $em->persist($reservation);

        // Mettre à jour disponibilité
        $exemplaire->setDisponibilite(false);
        $em->flush();

        // Vérifications
        $this->assertFalse($exemplaire->isDisponibilite());
        $this->assertSame($user, $reservation->getEmprunteur());
        $this->assertSame($exemplaire, $reservation->getExemplaire());

        // Simuler retour de l'exemplaire et assignation à la réservation suivante
        $exemplaire->setDisponibilite(true);
        $em->flush();
        $this->assertTrue($exemplaire->isDisponibilite());
    }
}
