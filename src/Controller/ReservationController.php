<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Exemplaire;
use App\Entity\Reservation;
use App\Repository\ReservationRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ReservationController extends AbstractController
{
#[Route('/mes-reservations', name: 'app_mes_reservations')]
    public function mesReservations(ReservationRepository $repo): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException("Vous devez être connecté.");
        }

        // On récupère toutes les réservations de l'utilisateur
        $reservations = $repo->findBy(['emprunteur' => $user]);

        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservations
        ]);
    }

    #[Route('/reservation/{id}/rendre', name: 'reservation_rendre')]
public function rendre(Reservation $reservation, EntityManagerInterface $em): Response
{
    $ouvrage = $reservation->getOuvrage();
    $exemplaire = $reservation->getExemplaire();

    if ($exemplaire === null) {
        $em->remove($reservation);
        $em->flush();

        $this->addFlash('warning', "La réservation a été annulée car aucun exemplaire n'était attribué.");
        return $this->redirectToRoute('app_mes_reservations');
    }

    // 1. Définir la date de retour réel
    $reservation->setDateRetourReel(new \DateTime());

    // 2. Chercher la prochaine réservation en attente
    $nextReservation = $em->getRepository(Reservation::class)->createQueryBuilder('r')
        ->where('r.ouvrage = :ouvrage')
        ->andWhere('r.exemplaire IS NULL')
        ->orderBy('r.id', 'ASC') // plus ancienne réservation d’abord
        ->setParameter('ouvrage', $ouvrage)
        ->setMaxResults(1)
        ->getQuery()
        ->getOneOrNullResult();

    if ($nextReservation) {
        // Attribuer l'exemplaire
        $nextReservation->setExemplaire($exemplaire);
        $nextReservation->setDateEmprunt(new \DateTime());

        // Calcul de la date de retour prévu depuis la catégorie
        $duree = 0;

        foreach ($ouvrage->getCategories() as $categorie) {
            $duree = max($duree, $categorie->getDureeEmprunt());
        }

        $nextReservation->setDateRetourPrevu(
            (new \DateTime())->modify("+{$duree} days")
        );

        $exemplaire->setDisponibilite(false);
    } else {
        // Personne n'attend → l'exemplaire redevient disponible
        $exemplaire->setDisponibilite(true);
    }

    $em->flush();
    return $this->redirectToRoute('app_mes_reservations');
}
}
