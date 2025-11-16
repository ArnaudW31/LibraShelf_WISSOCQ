<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
#[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTime $dateEmprunt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $dateRetourPrevu = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $dateRetourReel = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $emprunteur = null;

    #[ORM\ManyToOne(targetEntity: Exemplaire::class, inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Exemplaire $exemplaire = null;

    /**
     * @var Collection<int, Exemplaire>
     */
    #[ORM\OneToMany(targetEntity: Exemplaire::class, mappedBy: 'reservation')]
    private Collection $exemplaires;

    public function __construct()
    {
        $this->exemplaires = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateEmprunt(): ?\DateTime
    {
        return $this->dateEmprunt;
    }

    public function setDateEmprunt(\DateTime $dateEmprunt): static
    {
        $this->dateEmprunt = $dateEmprunt;
        return $this;
    }

    public function getDateRetourPrevu(): ?\DateTime
    {
        return $this->dateRetourPrevu;
    }

    public function setDateRetourPrevu(?\DateTime $dateRetourPrevu): static
    {
        $this->dateRetourPrevu = $dateRetourPrevu;
        return $this;
    }

    public function getDateRetourReel(): ?\DateTime
    {
        return $this->dateRetourReel;
    }

    public function setDateRetourReel(?\DateTime $dateRetourReel): static
    {
        $this->dateRetourReel = $dateRetourReel;
        return $this;
    }

    public function getEmprunteur(): ?Utilisateur
    {
        return $this->emprunteur;
    }

    public function setEmprunteur(?Utilisateur $emprunteur): static
    {
        $this->emprunteur = $emprunteur;
        return $this;
    }

    public function getExemplaire(): ?Exemplaire
    {
        return $this->exemplaire;
    }

    public function setExemplaire(?Exemplaire $exemplaire): static
    {
        $this->exemplaire = $exemplaire;
        return $this;
    }

    public function isLate(): bool
    {
        if ($this->dateRetourReel === null || $this->dateRetourPrevu === null) {
            return false;
        }

        return $this->dateRetourReel > $this->dateRetourPrevu;
    }
}
