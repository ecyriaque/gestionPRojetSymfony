<?php

namespace App\Entity;

use App\Repository\FactureRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FactureRepository::class)]
class Facture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $factureid = null;

    #[ORM\ManyToOne(targetEntity: Projet::class)]
    #[ORM\JoinColumn(name: "projetid", referencedColumnName: "projetid", nullable: false)]
    private ?Projet $projet = null;

    #[ORM\Column(type: "date")]
    private ?\DateTimeInterface $dateemission = null;

    #[ORM\Column(type: "decimal", precision: 15, scale: 2)]
    private ?string $montanttotal = null;

    #[ORM\Column(length: 20)]
    #[ORM\Check("etat IN ('En attente', 'Payee', 'Annulee')")]
    private ?string $etat = null;

    // Getters and Setters

    public function getFactureid(): ?int
    {
        return $this->factureid;
    }

    public function setFactureid(int $factureid): self
    {
        $this->factureid = $factureid;
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

    public function getDateemission(): ?\DateTimeInterface
    {
        return $this->dateemission;
    }

    public function setDateemission(\DateTimeInterface $dateemission): self
    {
        $this->dateemission = $dateemission;
        return $this;
    }

    public function getMontanttotal(): ?string
    {
        return $this->montanttotal;
    }

    public function setMontanttotal(string $montanttotal): self
    {
        $this->montanttotal = $montanttotal;
        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): self
    {
        $this->etat = $etat;
        return $this;
    }
}
