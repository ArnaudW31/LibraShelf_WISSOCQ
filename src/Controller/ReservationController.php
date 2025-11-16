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
        // Date de retour réel = maintenant
        $reservation->setDateRetourReel(new \DateTime());

        // L'exemplaire devient disponible
        $exemplaire = $reservation->getExemplaire();
        $exemplaire->setDisponibilite(true);

        // Vérifie le retard
        $isLate = $reservation->isLate();

        $em->flush();

        if ($isLate) {
            $this->addFlash('danger', 'Exemplaire rendu en retard (c pas bi1)');
        } else {
            $this->addFlash('success', 'Exemplaire rendu à temps.');
        }

        return $this->redirectToRoute('app_mes_reservations');
    }
}
