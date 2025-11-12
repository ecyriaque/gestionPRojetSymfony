<?php

namespace App\Entity;

use App\Repository\FormationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FormationRepository::class)]
class Formation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $formationid = null;

    #[ORM\ManyToOne(targetEntity: SessionFormation::class)]
    #[ORM\JoinColumn(name: 'sessionid', referencedColumnName: 'sessionid', nullable: false)]
    private ?SessionFormation $session = null;

    #[ORM\Column(length: 255)]
    private ?string $organisme = null;


    #[ORM\Column(name: 'couht', type: 'decimal', precision: 15, scale: 2)]
    private ?string $cout = null;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
    private ?string $tauxtva = null;

    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $dateformation = null;

    // Getters and Setters

    public function getFormationid(): ?int
    {
        return $this->formationid;
    }

    public function setFormationid(int $formationid): self
    {
        $this->formationid = $formationid;

        return $this;
    }

    public function getSession(): ?SessionFormation
    {
        return $this->session;
    }

    public function setSession(SessionFormation $session): self
    {
        $this->session = $session;

        return $this;
    }

    public function getOrganisme(): ?string
    {
        return $this->organisme;
    }

    public function setOrganisme(string $organisme): self
    {
        $this->organisme = $organisme;

        return $this;
    }

    public function getCout(): ?string
    {
        return $this->cout;
    }

    public function setCout(string $cout): self
    {
        $this->cout = $cout;
        return $this;
    }


    public function getCouht(): ?string
    {
        return $this->cout;
    }


    public function setCouht(string $couht): self
    {
        $this->cout = $couht;

        return $this;
    }

    public function getTauxtva(): ?string
    {
        return $this->tauxtva;
    }

    public function setTauxtva(string $tauxtva): self
    {
        $this->tauxtva = $tauxtva;

        return $this;
    }

    public function getDateformation(): ?\DateTimeInterface
    {
        return $this->dateformation;
    }

    public function setDateformation(\DateTimeInterface $dateformation): self
    {
        $this->dateformation = $dateformation;

        return $this;
    }
}
