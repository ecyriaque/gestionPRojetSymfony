<?php

namespace App\Entity;

use App\Repository\ApiKeyRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: ApiKeyRepository::class)]
#[ORM\Table(name: 'api_key')]
class ApiKey implements UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private string $name;

    #[ORM\Column(length: 64, unique: true)]
    private string $keyHash;

    #[ORM\Column(length: 45, nullable: true)]
    private ?string $allowedIp = null;

    #[ORM\Column]
    private bool $isActive = true;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $lastUsedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getName(): string { return $this->name; }
    public function setName(string $name): static { $this->name = $name; return $this; }

    public function getKeyHash(): string { return $this->keyHash; }
    public function setKeyHash(string $keyHash): static { $this->keyHash = $keyHash; return $this; }

    public function getAllowedIp(): ?string { return $this->allowedIp; }
    public function setAllowedIp(?string $allowedIp): static { $this->allowedIp = $allowedIp; return $this; }

    public function isActive(): bool { return $this->isActive; }
    public function setIsActive(bool $isActive): static { $this->isActive = $isActive; return $this; }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }

    public function getLastUsedAt(): ?\DateTimeImmutable { return $this->lastUsedAt; }
    public function setLastUsedAt(?\DateTimeImmutable $lastUsedAt): static { $this->lastUsedAt = $lastUsedAt; return $this; }

    // UserInterface
    public function getRoles(): array { return ['ROLE_API']; }
    public function getUserIdentifier(): string { return $this->name; }
    public function eraseCredentials(): void {}
}
