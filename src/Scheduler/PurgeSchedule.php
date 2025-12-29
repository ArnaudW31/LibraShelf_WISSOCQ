<?php

namespace App\Scheduler;

use App\Message\PurgeMessage;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;
use Symfony\Contracts\Cache\CacheInterface;

#[AsSchedule('PurgeSchedule')]
final class PurgeSchedule implements ScheduleProviderInterface
{
    public function __construct(
        private CacheInterface $cache,
    ) {
    }

    public function getSchedule(): Schedule
    {
        //Bon là j'avoue j'ai rien compris avec les cron sur la doc donc j'ai juste repris ce que symfony a généré
        return (new Schedule())
            ->add(
                RecurringMessage::every('30 days', new PurgeMessage()),
            )
            ->stateful($this->cache)
        ;
    }
}
