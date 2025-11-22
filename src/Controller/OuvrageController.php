<?php

namespace App\Controller;

use App\Repository\OuvrageRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Reservation;
use App\Entity\Ouvrage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
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
    Security $security,
    EntityManagerInterface $em
): Response {
    $user = $security->getUser();

    // Cherche un exemplaire disponible
    $exemplaireDispo = null;

    foreach ($ouvrage->getExemplaires() as $ex) {
        if ($ex->isDisponibilite()) {
            $exemplaireDispo = $ex;
            break;
        }
    }

    // EXEMPLAIRE DISPONIBLE -> EMPRUNT IMMÉDIAT
    if ($exemplaireDispo) {
        $reservation = new Reservation();
        $reservation->setOuvrage($ouvrage);
        $reservation->setExemplaire($exemplaireDispo);
        $reservation->setEmprunteur($user);
        $reservation->setDateEmprunt(new \DateTime());

        // Calcul de la date de retour prévu depuis la catégorie
        $duree = 0;

        foreach ($ouvrage->getCategories() as $categorie) {
            $duree = max($duree, $categorie->getDureeEmprunt());
        }
        $reservation->setDateRetourPrevu(
            (new \DateTime())->modify("+{$duree} days")
        );

        // Exemplaire devient indisponible
        $exemplaireDispo->setDisponibilite(false);

        $em->persist($reservation);
        $em->flush();

        $this->addFlash('success', 'Exemplaire emprunté !');
        return $this->redirectToRoute('app_mes_reservations');
    }

    // PAS D’EXEMPLAIRE → réservation en file d'attente
    $reservation = new Reservation();
    $reservation->setOuvrage($ouvrage);
    $reservation->setEmprunteur($user);
    $reservation->setDateEmprunt(null); // pas encore emprunté
    $reservation->setDateRetourPrevu(null);

    $em->persist($reservation);
    $em->flush();

    $this->addFlash('info', 'Aucun exemplaire disponible : vous êtes ajouté à la file d’attente.');
    return $this->redirectToRoute('app_mes_reservations');
}
}
