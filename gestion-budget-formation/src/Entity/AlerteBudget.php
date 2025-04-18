<?php

namespace App\Entity;

use App\Repository\AlerteBudgetRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AlerteBudgetRepository::class)]
class AlerteBudget
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $alerteid = null;

    #[ORM\ManyToOne(targetEntity: Projet::class)]
    #[ORM\JoinColumn(name: "projetid", referencedColumnName: "projetid", nullable: false)]
    private ?Projet $projet = null;

    #[ORM\Column(type: "decimal", precision: 15, scale: 2)]
    private ?string $montantdepasse = null;

    #[ORM\Column(type: "date")]
    private ?\DateTimeInterface $datealerte = null;

    // Getters and Setters

    public function getAlerteid(): ?int
    {
        return $this->alerteid;
    }

    public function setAlerteid(int $alerteid): self
    {
        $this->alerteid = $alerteid;
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

    public function getMontantdepasse(): ?string
    {
        return $this->montantdepasse;
    }

    public function setMontantdepasse(string $montantdepasse): self
    {
        $this->montantdepasse = $montantdepasse;
        return $this;
    }

    public function getDatealerte(): ?\DateTimeInterface
    {
        return $this->datealerte;
    }

    public function setDatealerte(\DateTimeInterface $datealerte): self
    {
        $this->datealerte = $datealerte;
        return $this;
    }
}
