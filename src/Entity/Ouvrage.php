<?php

namespace App\Entity;

use App\Repository\OuvrageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OuvrageRepository::class)]
class Ouvrage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    //Titre du livre
    #[Assert\NotBlank(message: 'Le titre est obligatoire')]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: 'Le titre doit contenir au moins 3 caractères',
        maxMessage: 'Le titre ne peut pas dépasser 255 caractères'
    )]
    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    //Sa liste d'auteurs
    /**
     * @var Collection<int, Auteur>
     */
    #[ORM\ManyToMany(targetEntity: Auteur::class, inversedBy: 'ouvrages')]
    private Collection $auteurs;

    //Son éditeur (je me suis dit qu'on avait juste besoin du nom de l'éditeur donc pas de table Editeur)
    #[ORM\Column(length: 255)]
    private ?string $editeur = null;

    //ISBN
    #[ORM\Column(length: 13)]
    #[Assert\NotBlank(message: 'L\'ISBN est obligatoire')]
    #[Assert\Isbn(
        type: Assert\Isbn::ISBN_13,
        message: 'L\'ISBN "{{ value }}" n\'est pas valide (ISBN-13 attendu)'
    )]
    private ?string $isbn = null;

    /**
     * @var Collection<int, Categorie>
     */
    #[ORM\ManyToMany(targetEntity: Categorie::class, mappedBy: 'ouvrages')]
    private Collection $categories;

    /**
     * @var Collection<int, Tags>
     */
    #[ORM\ManyToMany(targetEntity: Tags::class, mappedBy: 'ouvrages')]
    private Collection $tags;

    //La date de parution
    #[Assert\NotNull]
    #[Assert\LessThanOrEqual('today')]
    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $parution = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $resume = null;

    //Les exemplaires associés à cet ouvrage
    /**
     * @var Collection<int, Exemplaire>
     */
    #[ORM\OneToMany(targetEntity: Exemplaire::class, mappedBy: 'ouvrage', cascade: ['persist', 'remove'])]
    private Collection $exemplaires;

    //Les réservations sont associés à des ouvrages comme ça quand un exemplaire se libère, on sait quelles réservations doivent être màj
    /**
     * @var Collection<int, Reservation>
     */
    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'ouvrage')]
    private Collection $reservations;

    public function __construct()
    {
        $this->auteurs = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->exemplaires = new ArrayCollection();
        $this->reservations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * @return Collection<int, Auteur>
     */
    public function getAuteurs(): Collection
    {
        return $this->auteurs;
    }

    public function addAuteur(Auteur $auteur): static
    {
        if (!$this->auteurs->contains($auteur)) {
            $this->auteurs->add($auteur);
        }

        return $this;
    }

    public function removeAuteur(Auteur $auteur): static
    {
        $this->auteurs->removeElement($auteur);

        return $this;
    }

    public function getEditeur(): ?string
    {
        return $this->editeur;
    }

    public function setEditeur(string $editeur): static
    {
        $this->editeur = $editeur;

        return $this;
    }

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(string $isbn): static
    {
        $this->isbn = $isbn;

        return $this;
    }

    /**
     * @return Collection<int, Categorie>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Categorie $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->addOuvrage($this);
        }

        return $this;
    }

    public function removeCategory(Categorie $category): static
    {
        if ($this->categories->removeElement($category)) {
            $category->removeOuvrage($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Tags>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tags $tag): static
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
            $tag->addOuvrage($this);
        }

        return $this;
    }

    public function removeTag(Tags $tag): static
    {
        if ($this->tags->removeElement($tag)) {
            $tag->removeOuvrage($this);
        }

        return $this;
    }

    public function getParution(): ?\DateTimeImmutable
    {
        return $this->parution;
    }

    public function setParution(\DateTimeImmutable $parution): static
    {
        $this->parution = $parution;

        return $this;
    }

    public function getResume(): ?string
    {
        return $this->resume;
    }

    public function setResume(?string $resume): static
    {
        $this->resume = $resume;

        return $this;
    }

    public function getExemplaires(): Collection
    {
        return $this->exemplaires;
    }

    public function addExemplaire(Exemplaire $exemplaire): static
    {
        if (!$this->exemplaires->contains($exemplaire)) {
            $this->exemplaires->add($exemplaire);
            $exemplaire->setOuvrage($this); // IMPORTANT
        }

        return $this;
    }

    public function removeExemplaire(Exemplaire $exemplaire): static
    {
        if ($this->exemplaires->removeElement($exemplaire)) {
            if ($exemplaire->getOuvrage() === $this) {
                $exemplaire->setOuvrage(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): static
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setOuvrage($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): static
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getOuvrage() === $this) {
                $reservation->setOuvrage(null);
            }
        }

        return $this;
    }
}
