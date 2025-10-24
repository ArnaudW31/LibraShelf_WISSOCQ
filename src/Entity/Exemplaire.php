<?php

namespace App\Entity;

use App\Enum\Etat;
use App\Repository\ExemplaireRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExemplaireRepository::class)]
class Exemplaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cote = null;

    #[ORM\Column(enumType: Etat::class)]
    private ?Etat $etat = null;

    #[ORM\Column]
    private ?bool $disponibilite = null;

    #[ORM\ManyToOne(inversedBy: 'exemplaires')]
    private ?Reservation $reservation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCote(): ?string
    {
        return $this->cote;
    }

    public function setCote(?string $cote): static
    {
        $this->cote = $cote;

        return $this;
    }

    public function getEtat(): ?Etat
    {
        return $this->etat;
    }

    public function setEtat(Etat $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    public function isDisponibilite(): ?bool
    {
        return $this->disponibilite;
    }

    public function setDisponibilite(bool $disponibilite): static
    {
        $this->disponibilite = $disponibilite;

        return $this;
    }

    public function getReservation(): ?Reservation
    {
        return $this->reservation;
    }

    public function setReservation(?Reservation $reservation): static
    {
        $this->reservation = $reservation;

        return $this;
    }
}
