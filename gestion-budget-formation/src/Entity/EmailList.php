<?php

namespace App\Entity;

use App\Repository\EmailListRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmailListRepository::class)]
class EmailList
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\ManyToOne(targetEntity: ListeDiffusion::class)]
    #[ORM\JoinColumn(name: "listeid", referencedColumnName: "listeid", nullable: false)]
    private ?ListeDiffusion $listeDiffusion = null;

    // Getters and Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getListeDiffusion(): ?ListeDiffusion
    {
        return $this->listeDiffusion;
    }

    public function setListeDiffusion(ListeDiffusion $listeDiffusion): self
    {
        $this->listeDiffusion = $listeDiffusion;
        return $this;
    }
}
