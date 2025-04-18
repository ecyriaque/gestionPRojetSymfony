<?php

namespace App\Entity;

use App\Repository\AppelDeFondsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AppelDeFondsRepository::class)]
class AppelDeFonds
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $appelid = null;

    #[ORM\ManyToOne(targetEntity: Projet::class)]
    #[ORM\JoinColumn(name: "projetid", referencedColumnName: "projetid", nullable: false)]
    private ?Projet $projet = null;

    #[ORM\Column(type: "decimal", precision: 15, scale: 2)]
    private ?string $montantdemande = null;

    #[ORM\Column(type: "date")]
    private ?\DateTimeInterface $datedemande = null;

    // Getters and Setters

    public function getAppelid(): ?int
    {
        return $this->appelid;
    }

    public function setAppelid(int $appelid): self
    {
        $this->appelid = $appelid;
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

    public function getMontantdemande(): ?string
    {
        return $this->montantdemande;
    }

    public function setMontantdemande(string $montantdemande): self
    {
        $this->montantdemande = $montantdemande;
        return $this;
    }

    public function getDatedemande(): ?\DateTimeInterface
    {
        return $this->datedemande;
    }

    public function setDatedemande(\DateTimeInterface $datedemande): self
    {
        $this->datedemande = $datedemande;
        return $this;
    }
}
