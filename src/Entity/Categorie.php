<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategorieRepository::class)]
class Categorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom de la catégorie est obligatoire.')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Nan mais ho c\'est le nom de la catégorie pas un roman ici.'
    )]
    private ?string $nom = null;

    /**
     * @var Collection<int, Ouvrage>
     */
    #[ORM\ManyToMany(targetEntity: Ouvrage::class, inversedBy: 'categories')]
    private Collection $ouvrages;

    //La durée d'emprunt d'un exemplaire est définie par ses catégories (on prends le maxi)
    #[ORM\Column]
    #[Assert\NotNull(message: 'La durée d\'emprunt est obligatoire.')]
    #[Assert\Positive(message: 'La durée d\'emprunt doit être un nombre positif.')]
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
