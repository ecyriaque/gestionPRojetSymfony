<?php

namespace App\Entity;

use App\Repository\ProjetRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProjetRepository::class)]
class Projet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $projetid = null;

   #[ORM\ManyToOne(targetEntity: Client::class)]
    #[ORM\JoinColumn(name: "clientid", referencedColumnName: "clientid", nullable: false)]
    private ?Client $client = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: "referentid", referencedColumnName: "utilisateurid", nullable: false)]
    private ?Utilisateur $referent = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: "decimal", precision: 15, scale: 2)]
    #[Assert\Positive(message: "Le montant doit être un nombre positif.")]
    private ?string $budgetinitial = null;

    #[ORM\Column(type: "decimal", precision: 15, scale: 2)]
    #[Assert\Positive(message: "Le seuil d'alerte doit être un nombre positif.")]
    private ?string $seuilalerte = null;

    // Getters and Setters

    public function getProjetid(): ?int
    {
        return $this->projetid;
    }

    public function setProjetid(int $projetid): self
    {
        $this->projetid = $projetid;
        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(Client $client): self
    {
        $this->client = $client;
        return $this;
    }

    public function getReferent(): ?Utilisateur
    {
        return $this->referent;
    }

    public function setReferent(Utilisateur $referent): self
    {
        $this->referent = $referent;
        return $this;
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

    public function getBudgetinitial(): ?string
    {
        return $this->budgetinitial;
    }

    public function setBudgetinitial(string $budgetinitial): self
    {
        $this->budgetinitial = $budgetinitial;
        return $this;
    }

    public function getSeuilalerte(): ?string
    {
        return $this->seuilalerte;
    }

    public function setSeuilalerte(string $seuilalerte): self
    {
        $this->seuilalerte = $seuilalerte;
        return $this;
    }
}
