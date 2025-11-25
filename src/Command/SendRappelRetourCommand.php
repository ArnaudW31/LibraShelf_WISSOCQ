<?php

namespace App\Command;

use App\Message\RappelRetourMessage;
use App\Repository\ReservationRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:send:return-reminders',
    description: 'Envoie les rappels J-7, J-0 et J+3'
)]
class SendRappelRetourCommand extends Command
{
    public function __construct(
        private ReservationRepository $reservationRepository,
        private MessageBusInterface $bus
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $today = new \DateTimeImmutable();

        $reminders = [
            'J-7' => $today->modify('+7 days'),
            'J-0' => $today,
            'J+3' => $today->modify('-3 days'),
        ];

        foreach ($reminders as $type => $date) {
            $reservations = $this->reservationRepository->findByDateRetourPrevu($date);

            foreach ($reservations as $reservation) {
                $this->bus->dispatch(
                    new RappelRetourMessage($reservation->getId(), $type)
                );
            }
        }

        $output->writeln('Rappels envoyés.');

        return Command::SUCCESS;
    }
}
