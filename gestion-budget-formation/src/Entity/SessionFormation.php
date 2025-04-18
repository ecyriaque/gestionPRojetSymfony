<?php

namespace App\Entity;

use App\Repository\SessionFormationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SessionFormationRepository::class)]
class SessionFormation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $sessionid = null;

    #[ORM\ManyToOne(targetEntity: Projet::class)]
    #[ORM\JoinColumn(name: "projetid", referencedColumnName: "projetid", nullable: false)]
    private ?Projet $projet = null;

    #[ORM\Column(type: "date")]
    private ?\DateTimeInterface $datedebut = null;

    #[ORM\Column(type: "date")]
    private ?\DateTimeInterface $datefin = null;

    #[ORM\Column(type: "decimal", precision: 15, scale: 2)]
    private ?string $couttotal = null;

    // Getters and Setters

    public function getSessionid(): ?int
    {
        return $this->sessionid;
    }

    public function setSessionid(int $sessionid): self
    {
        $this->sessionid = $sessionid;
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

    public function getDatedebut(): ?\DateTimeInterface
    {
        return $this->datedebut;
    }

    public function setDatedebut(\DateTimeInterface $datedebut): self
    {
        $this->datedebut = $datedebut;
        return $this;
    }

    public function getDatefin(): ?\DateTimeInterface
    {
        return $this->datefin;
    }

    public function setDatefin(\DateTimeInterface $datefin): self
    {
        $this->datefin = $datefin;
        return $this;
    }

    public function getCouttotal(): ?string
    {
        return $this->couttotal;
    }

    public function setCouttotal(string $couttotal): self
    {
        $this->couttotal = $couttotal;
        return $this;
    }
}
