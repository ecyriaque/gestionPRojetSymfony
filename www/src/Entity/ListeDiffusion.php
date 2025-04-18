<?php

namespace App\Entity;

use App\Repository\ListeDiffusionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ListeDiffusionRepository::class)]
class ListeDiffusion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $listeid = null;

    #[ORM\ManyToOne(targetEntity: Projet::class)]
    #[ORM\JoinColumn(name: 'projetid', referencedColumnName: 'projetid', nullable: false)]
    private ?Projet $projet = null;

    // Getters and Setters

    public function getListeid(): ?int
    {
        return $this->listeid;
    }

    public function setListeid(int $listeid): self
    {
        $this->listeid = $listeid;

        return $this;
    }

    public function getProjet(): ?Projet
    {
        return $this->projet;
    }

    public function setProjet(Projet $projet): self
    {
        $this->projet = $projet;

        return $this;
    }
}
