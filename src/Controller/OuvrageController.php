<?php

namespace App\Controller;

use App\Repository\OuvrageRepository;
use App\Repository\ExemplaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Reservation;
use App\Entity\Ouvrage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class OuvrageController extends AbstractController
{
#[Route('/ouvrages', name: 'app_ouvrages')]
    public function index(OuvrageRepository $repo): Response
    {
        $ouvrages = $repo->findAll();

        return $this->render('ouvrage/index.html.twig', [
            'ouvrages' => $ouvrages
        ]);
    }

#[Route('/ouvrage/{id}/reserver', name: 'app_reserver_ouvrage')]
public function reserver(
    Ouvrage $ouvrage,
    ExemplaireRepository $exRepo,
    EntityManagerInterface $em
): Response {

    $user = $this->getUser();
    if (!$user) {
        throw $this->createAccessDeniedException("Vous devez être connecté.");
    }

    // 1) On cherche un exemplaire disponible
    $exemplaire = $exRepo->findOneBy([
        'ouvrage' => $ouvrage,
        'disponibilite' => true
    ]);

    if (!$exemplaire) {
        $this->addFlash('error', 'Aucun exemplaire disponible pour cet ouvrage.');
        return $this->redirectToRoute('app_ouvrages');
    }

    // 2) On crée une réservation
    $reservation = new Reservation();
    $reservation->setEmprunteur($user);
    $reservation->setDateEmprunt(new \DateTime());
    $reservation->setExemplaire($exemplaire); // OU setExemplaire() selon ton modèle

    // 3) L'exemplaire passe en indisponible
    $exemplaire->setDisponibilite(false);
    $exemplaire->setReservation($reservation);

    // 4) On persiste
    $em->persist($reservation);
    $em->persist($exemplaire);
    $em->flush();

    $this->addFlash('success', 'Exemplaire réservé et emprunté !');
    return $this->redirectToRoute('app_ouvrages');
}
}
