<?php

namespace App\MessageHandler;

use App\Message\RappelRetourMessage;
use App\Repository\ReservationRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Email;

#[AsMessageHandler]
class RappelRetourMessageHandler
{
    public function __construct(
        private ReservationRepository $reservationRepository,
        private MailerInterface $mailer
    ) {
    }

    public function __invoke(RappelRetourMessage $message)
    {
        $reservation = $this->reservationRepository->find($message->getReservationId());
        if (!$reservation) {
            return;
        }

        $user = $reservation->getEmprunteur();
        $ouvrage = $reservation->getOuvrage();
        $type = $message->getType();

        $subject = match ($type) {
            'J-7' => 'Rappel : retour dans 7 jours',
            'J-0' => 'Rappel : retour aujourd’hui',
            'J+3' => 'Retard : retour dépassé de 3 jours !',
            default => 'Rappel de retour'
        };

        // ok je vais ptet un peu loin
        if ('J+3' == $type) {
            $body = "<p>ok ptit fdp tu trouves ça marrant de garder les livres sans jamais les rendre ?</p>
            <p>et bien sache que chez librashelf ça nous fais vraiment pas rire donc si ce putain de livre n'est pas rendu d'ici demain</p>
            <p>on va venir te chercher par la peau du cul et on va le récupérer nous même, pigé ?";
        } else {
            $body = "<p>Bonjour {$user->getPrenom()},</p>
                <p>Votre emprunt du livre <strong>{$ouvrage->getTitre()}</strong> est concerné par un rappel <strong>{$type}</strong>.</p>
                <p>Date de retour prévue : {$reservation->getDateRetourPrevu()->format('d/m/Y')}</p>";
        }

        $email = (new Email())
            ->from('noreply@librashelf.com')
            ->to($user->getEmail())
            ->subject($subject)
            ->html($body);

        $this->mailer->send($email);
    }
}
