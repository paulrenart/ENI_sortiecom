<?php

namespace App\Entity;

use App\Repository\InscriptionsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InscriptionsRepository::class)
 */
class Inscriptions
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Sorties::class, inversedBy="inscriptions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $sorties_id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="inscriptions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSortiesId(): ?Sorties
    {
        return $this->sorties_id;
    }

    public function setSortiesId(?Sorties $sorties_id): self
    {
        $this->sorties_id = $sorties_id;

        return $this;
    }

    public function getUserId(): ?User
    {
        return $this->user_id;
    }

    public function setUserId(?User $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }
}
