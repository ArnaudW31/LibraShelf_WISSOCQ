<?php

namespace App\Scheduler\Handler;

use App\Message\PurgeMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Doctrine\ORM\EntityManagerInterface;

#[AsMessageHandler]
class PurgeMessageHandler
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    public function __invoke(PurgeMessage $message): void
    {
        $date = new \DateTimeImmutable('-30 days');

        $this->em->createQueryBuilder()
            ->delete('App\Entity\Reservation', 'r')
            ->where('r.dateRetourReel < :limit')
            ->setParameter('limit', $date)
            ->getQuery()
            ->execute();
    }
}