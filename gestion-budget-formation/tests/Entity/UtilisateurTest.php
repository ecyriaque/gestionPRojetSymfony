<?php

namespace App\Tests\Entity;

use App\Entity\Utilisateur;
use PHPUnit\Framework\TestCase;

class UtilisateurTest extends TestCase
{
    private Utilisateur $utilisateur;

    protected function setUp(): void
    {
        $this->utilisateur = new Utilisateur();
    }

    public function testGetSetUtilisateurId(): void
    {
        $this->utilisateur->setUtilisateurid(42);
        $this->assertSame(42, $this->utilisateur->getUtilisateurid());
    }

    public function testSetAndGetNom(): void
    {
        $this->utilisateur->setNom('Jean Dupont');
        $this->assertSame('Jean Dupont', $this->utilisateur->getNom());
    }

    public function testSetAndGetEmail(): void
    {
        $this->utilisateur->setEmail('jean.dupont@example.com');
        $this->assertSame('jean.dupont@example.com', $this->utilisateur->getEmail());
    }

    public function testSetAndGetMotDePasse(): void
    {
        $this->utilisateur->setMotdepasse('password123');
        $this->assertSame('password123', $this->utilisateur->getMotdepasse());
        $this->assertSame('password123', $this->utilisateur->getPassword());
    }

    public function testSetAndGetRole(): void
    {
        $this->utilisateur->setRole('Admin');
        $this->assertSame('Admin', $this->utilisateur->getRole());
        $this->assertContains('ROLE_ADMIN', $this->utilisateur->getRoles());

        $this->utilisateur->setRole('Gestionnaire');
        $this->assertSame('Gestionnaire', $this->utilisateur->getRole());
        $this->assertContains('ROLE_GESTIONNAIRE', $this->utilisateur->getRoles());

        $this->utilisateur->setRole('Unknown');
        $this->assertSame('Unknown', $this->utilisateur->getRole());
        $this->assertContains('ROLE_USER', $this->utilisateur->getRoles()); // Par défaut
    }

    public function testUserIdentifier(): void
    {
        $this->utilisateur->setEmail('test@example.com');
        $this->assertSame('test@example.com', $this->utilisateur->getUserIdentifier());
    }

    /**
     * Test de la validation du rôle Admin
     */
    public function testAdminRole(): void
    {
        $this->utilisateur->setRole('Admin');
        $roles = $this->utilisateur->getRoles();
        
        $this->assertIsArray($roles);
        $this->assertCount(1, $roles);
        $this->assertSame('ROLE_ADMIN', $roles[0]);
    }

    /**
     * Test de la validation du rôle Gestionnaire
     */
    public function testGestionnaireRole(): void
    {
        $this->utilisateur->setRole('Gestionnaire');
        $roles = $this->utilisateur->getRoles();
        
        $this->assertIsArray($roles);
        $this->assertCount(1, $roles);
        $this->assertSame('ROLE_GESTIONNAIRE', $roles[0]);
    }

    /**
     * Test de la méthode eraseCredentials
     */
    public function testEraseCredentials(): void
    {
        // Cette méthode est vide mais doit être implémentée pour l'interface
        // On teste qu'elle n'altère pas le mot de passe
        $this->utilisateur->setMotdepasse('password123');
        $this->utilisateur->eraseCredentials();
        
        $this->assertSame('password123', $this->utilisateur->getMotdepasse());
    }
    
    /**
     * Test avec différentes structures d'emails
     */
    public function testDifferentEmailFormats(): void
    {
        $emailsToTest = [
            'simple@example.com',
            'more.complex@sub.domain.example.com',
            'with-dash@example.com',
            'with_underscore@example.com',
            'with.dot@example.com'
        ];
        
        foreach ($emailsToTest as $email) {
            $this->utilisateur->setEmail($email);
            $this->assertSame($email, $this->utilisateur->getEmail());
            $this->assertSame($email, $this->utilisateur->getUserIdentifier());
        }
    }
} 