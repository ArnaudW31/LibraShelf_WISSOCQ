<?php

namespace App\Scheduler\Handler;

use App\Message\PurgeMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

#[AsMessageHandler]
class PurgeMessageHandler
{
    public function __construct(
        private EntityManagerInterface $em,
        private LoggerInterface $logger
    ) {}

    public function __invoke(PurgeMessage $message): void
    {
        //On vire les emprunts qui ont eu lieu il y a + de 30 jours tout les 30 jours
        $date = new \DateTimeImmutable('-30 days');

        $this->em->createQueryBuilder()
            ->delete('App\Entity\Reservation', 'r')
            ->where('r.dateRetourReel < :limit')
            ->setParameter('limit', $date)
            ->getQuery()
            ->execute();

        $this->logger->info('Purge réalisée', [
            'limit' => $date->format('Y-m-d'),
]);
    }
}