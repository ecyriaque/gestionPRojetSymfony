<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $clientid = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 14, unique: true)]
    private ?string $siren = null;

    #[ORM\Column(length: 34)]
    private ?string $iban = null;

    #[ORM\Column(type: "text")]
    private ?string $adresse = null;

    #[ORM\Column(type: "decimal", precision: 2, scale: 2)]
    private ?string $commission = null;

    #[ORM\Column(length: 255)]
    private ?string $emailcontactfacturation = null;

    // Getters and Setters

    public function getClientid(): ?int
    {
        return $this->clientid;
    }

    public function setClientid(int $clientid): self
    {
        $this->clientid = $clientid;
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

    public function getSiren(): ?string
    {
        return $this->siren;
    }

    public function setSiren(string $siren): self
    {
        $this->siren = $siren;
        return $this;
    }

    public function getIban(): ?string
    {
        return $this->iban;
    }

    public function setIban(string $iban): self
    {
        $this->iban = $iban;
        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;
        return $this;
    }

    public function getCommission(): ?string
    {
        return $this->commission;
    }

    public function setCommission(string $commission): self
    {
        $this->commission = $commission;
        return $this;
    }

    public function getEmailcontactfacturation(): ?string
    {
        return $this->emailcontactfacturation;
    }

    public function setEmailcontactfacturation(string $emailcontactfacturation): self
    {
        $this->emailcontactfacturation = $emailcontactfacturation;
        return $this;
    }
}
