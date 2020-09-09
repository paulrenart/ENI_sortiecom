<?php

namespace App\Entity;

use App\Repository\SortiesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SortiesRepository::class)
 */
class Sorties
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_debut;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_fin;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photo;

    /**
     * @ORM\Column(type="integer")
     */
    private $etat;

    /**
     * @ORM\OneToMany(targetEntity=Inscriptions::class, mappedBy="sorties_id")
     */
    private $inscriptions;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="sorties")
     * @ORM\JoinColumn(nullable=false)
     */
    private $organisateur_id;

    /**
     * @ORM\ManyToOne(targetEntity=Lieux::class, inversedBy="sorties")
     * @ORM\JoinColumn(nullable=false)
     */
    private $lieux_id;

    /**
     * @ORM\ManyToOne(targetEntity=Campus::class, inversedBy="sorties")
     * @ORM\JoinColumn(nullable=false)
     */
    private $campus_id;

    public function __construct()
    {
        $this->inscriptions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDateDebut(\DateTimeInterface $date_debut): self
    {
        $this->date_debut = $date_debut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setDateFin(\DateTimeInterface $date_fin): self
    {
        $this->date_fin = $date_fin;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getEtat(): ?int
    {
        return $this->etat;
    }

    public function setEtat(int $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * @return Collection|Inscriptions[]
     */
    public function getInscriptions(): Collection
    {
        return $this->inscriptions;
    }

    public function addInscription(Inscriptions $inscription): self
    {
        if (!$this->inscriptions->contains($inscription)) {
            $this->inscriptions[] = $inscription;
            $inscription->setSortiesId($this);
        }

        return $this;
    }

    public function removeInscription(Inscriptions $inscription): self
    {
        if ($this->inscriptions->contains($inscription)) {
            $this->inscriptions->removeElement($inscription);
            // set the owning side to null (unless already changed)
            if ($inscription->getSortiesId() === $this) {
                $inscription->setSortiesId(null);
            }
        }

        return $this;
    }

    public function getOrganisateurId(): ?User
    {
        return $this->organisateur_id;
    }

    public function setOrganisateurId(?User $organisateur_id): self
    {
        $this->organisateur_id = $organisateur_id;

        return $this;
    }

    public function getLieuxId(): ?Lieux
    {
        return $this->lieux_id;
    }

    public function setLieuxId(?Lieux $lieux_id): self
    {
        $this->lieux_id = $lieux_id;

        return $this;
    }

    public function getCampusId(): ?Campus
    {
        return $this->campus_id;
    }

    public function setCampusId(?Campus $campus_id): self
    {
        $this->campus_id = $campus_id;

        return $this;
    }
}
