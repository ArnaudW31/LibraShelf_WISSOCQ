<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}
