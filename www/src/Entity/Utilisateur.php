<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $utilisateurid = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $nom = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $motdepasse = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank]
    private ?string $role = null;

    // Getters and Setters

    public function getUtilisateurid(): ?int
    {
        return $this->utilisateurid;
    }

    public function setUtilisateurid(int $utilisateurid): self
    {
        $this->utilisateurid = $utilisateurid;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getMotdepasse(): ?string
    {
        return $this->motdepasse;
    }

    public function setMotdepasse(string $motdepasse): self
    {
        $this->motdepasse = $motdepasse;
        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;
        return $this;
    }

    // Implémentation des méthodes de l'interface UserInterface

    public function getUserIdentifier(): string
    {
        return $this->email; // Utilise l'email comme identifiant unique
    }

    public function getRoles(): array
    {
        if (strtolower($this->role) === 'admin') {
            return ['ROLE_ADMIN'];
        } elseif (strtolower($this->role) === 'gestionnaire') {
            return ['ROLE_GESTIONNAIRE'];
        }

        return ['ROLE_USER'];
    }

    public function eraseCredentials(): void
    {
        
    }

    public function getPassword(): string
    {
        return $this->motdepasse;
    }
}
