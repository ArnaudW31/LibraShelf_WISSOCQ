<?php

namespace App\Controller;

use App\Entity\Ouvrage;
use App\Entity\Reservation;
use App\Form\OuvrageFilterType;
use App\Message\EmailNotificationMessage;
use App\Repository\OuvrageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Psr\Log\LoggerInterface;

final class OuvrageController extends AbstractController
{
    //Route pour voir la liste des ouvrages
    #[Route('/ouvrages', name: 'app_ouvrages')]
    public function index(Request $request, OuvrageRepository $repo): Response
    {
        $form = $this->createForm(OuvrageFilterType::class, null, [
            'method' => 'get',
        ]);
        $form->handleRequest($request);

        $qb = $repo->createQueryBuilder('o')
        ->leftJoin('o.auteurs', 'a')
        ->leftJoin('o.categories', 'c')
        ->leftJoin('o.tags', 't')
        ->addSelect('a', 'c', 't')
        ->distinct(); // nécessaire à cause des jointures

        // gros IF pour le filtrage
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if (!empty($data['titre'])) {
                $qb->andWhere('o.titre LIKE :titre')
                ->setParameter('titre', '%'.$data['titre'].'%');
            }

            if (!empty($data['auteur'])) {
                $qb->andWhere(':auteur MEMBER OF o.auteurs')
                ->setParameter('auteur', $data['auteur']);
            }

            if (!empty($data['categorie'])) {
                $qb->andWhere(':categorie MEMBER OF o.categories')
                ->setParameter('categorie', $data['categorie']);
            }

            if (!empty($data['tag'])) {
                $qb->andWhere(':tag MEMBER OF o.tags')
                ->setParameter('tag', $data['tag']);
            }

            if (null !== $data['disponible']) {
                $qb->andWhere('EXISTS (
                    SELECT 1 FROM App\Entity\Exemplaire e
                    WHERE e.ouvrage = o.id AND (e.disponibilite = :dispo OR :dispo = false)
                )')
                ->setParameter('dispo', (bool) $data['disponible']);
            }
        }

        $ouvrages = $qb->getQuery()->getResult();

        return $this->render('ouvrage/index.html.twig', [
            'ouvrages' => $ouvrages,
            'form' => $form->createView(),
        ]);
    }

    //Route pour réseerver un exemplaire d'un ouvrage
    #[Route('/ouvrage/{id}/reserver', name: 'app_reserver_ouvrage')]
    public function reserver(
        Ouvrage $ouvrage,
        Security $security,
        EntityManagerInterface $em,
        MessageBusInterface $bus,
        LoggerInterface $logger
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

        // EXEMPLAIRE DISPON -> EMPRUNT
        if ($exemplaireDispo) {
            $reservation = new Reservation();
            $reservation->setOuvrage($ouvrage);
            $reservation->setExemplaire($exemplaireDispo);
            $reservation->setEmprunteur($user);
            $reservation->setDateEmprunt(new \DateTime());

            // Calcul de la date de retour prévu depuis la catégorie
            $duree = 0;

            // On prends la durée maxi parmis toutes les catégories de l'ouvrage (j'ai hésité avec le min, mais j'ai préféré prendre le max)
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

            // Notif à l'utilisateur
            $bus->dispatch(new EmailNotificationMessage(
                $this->getUser()->getEmail(),
                'Reservation confirmée',
                "Votre réservation du livre « {$ouvrage->getTitre()} » a bien été enregistrée."
            ));

            $this->addFlash('success', 'Exemplaire emprunté !');

            $logger->info("Nouvelle réservation effectuée", ['ouvrage_id' => $ouvrage->getId()]);

            return $this->redirectToRoute('app_mes_reservations');
        }

        // PAS D’EXEMPLAIRE -> réservation en file d'attente
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
