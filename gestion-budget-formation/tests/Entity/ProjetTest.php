<?php

namespace App\Tests\Entity;

use App\Entity\Projet;
use App\Entity\Client;
use App\Entity\Utilisateur;
use PHPUnit\Framework\TestCase;

class ProjetTest extends TestCase
{
    private Projet $projet;
    private Client $client;
    private Utilisateur $referent;

    protected function setUp(): void
    {
        $this->projet = new Projet();
        $this->client = new Client();
        $this->referent = new Utilisateur();
        
        // Configuration des mocks
        $this->client->setClientid(1);
        $this->client->setNom('Client Test');
        
        $this->referent->setUtilisateurid(1);
        $this->referent->setNom('Referent Test');
    }

    public function testGetSetProjetId(): void
    {
        $this->projet->setProjetid(42);
        $this->assertSame(42, $this->projet->getProjetid());
    }

    public function testGetSetClient(): void
    {
        $this->projet->setClient($this->client);
        $this->assertSame($this->client, $this->projet->getClient());
    }

    public function testGetSetReferent(): void
    {
        $this->projet->setReferent($this->referent);
        $this->assertSame($this->referent, $this->projet->getReferent());
    }

    public function testGetSetNom(): void
    {
        $this->projet->setNom('Projet Formation 2023');
        $this->assertSame('Projet Formation 2023', $this->projet->getNom());
    }

    public function testGetSetBudgetInitial(): void
    {
        $this->projet->setBudgetinitial('10000.00');
        $this->assertSame('10000.00', $this->projet->getBudgetinitial());
    }

    public function testGetSetSeuilAlerte(): void
    {
        $this->projet->setSeuilalerte('1000.00');
        $this->assertSame('1000.00', $this->projet->getSeuilalerte());
    }
} 