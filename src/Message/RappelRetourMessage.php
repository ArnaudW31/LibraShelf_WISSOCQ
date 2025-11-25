<?php

namespace App\Message;

class RappelRetourMessage
{
    public function __construct(
        private int $reservationId,
        private string $type // "J-7", "J-0", "J+3"
    ) {
    }

    public function getReservationId(): int
    {
        return $this->reservationId;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
