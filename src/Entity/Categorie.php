<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategorieRepository::class)]
class Categorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    /**
     * @var Collection<int, Ouvrage>
     */
    #[ORM\ManyToMany(targetEntity: Ouvrage::class, inversedBy: 'categories')]
    private Collection $ouvrages;

    #[ORM\Column]
    private ?int $dureeEmprunt = null;

    public function __construct()
    {
        $this->ouvrages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection<int, Ouvrage>
     */
    public function getOuvrages(): Collection
    {
        return $this->ouvrages;
    }

    public function addOuvrage(Ouvrage $ouvrage): static
    {
        if (!$this->ouvrages->contains($ouvrage)) {
            $this->ouvrages->add($ouvrage);
        }

        return $this;
    }

    public function removeOuvrage(Ouvrage $ouvrage): static
    {
        $this->ouvrages->removeElement($ouvrage);

        return $this;
    }

    public function getDureeEmprunt(): ?int
    {
        return $this->dureeEmprunt;
    }

    public function setDureeEmprunt(int $dureeEmprunt): static
    {
        $this->dureeEmprunt = $dureeEmprunt;

        return $this;
    }
}
