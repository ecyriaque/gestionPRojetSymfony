<?php

namespace App\Tests\Entity;

use App\Entity\AppelDeFonds;
use App\Entity\Projet;
use PHPUnit\Framework\TestCase;

class AppelDeFondsTest extends TestCase
{
    private AppelDeFonds $appelDeFonds;
    private Projet $projet;

    protected function setUp(): void
    {
        $this->appelDeFonds = new AppelDeFonds();
        $this->projet = new Projet();
        $this->projet->setProjetid(1);
        $this->projet->setNom('Projet Test');
    }

    public function testGetSetAppelId(): void
    {
        $this->appelDeFonds->setAppelid(42);
        $this->assertSame(42, $this->appelDeFonds->getAppelid());
    }

    public function testGetSetProjet(): void
    {
        $this->appelDeFonds->setProjet($this->projet);
        $this->assertSame($this->projet, $this->appelDeFonds->getProjet());
    }

    public function testGetSetMontantDemande(): void
    {
        $this->appelDeFonds->setMontantdemande('2500.00');
        $this->assertSame('2500.00', $this->appelDeFonds->getMontantdemande());
    }

    public function testGetSetDateDemande(): void
    {
        $date = new \DateTime('2023-06-15');
        $this->appelDeFonds->setDatedemande($date);
        $this->assertSame($date, $this->appelDeFonds->getDatedemande());
    }

    /**
     * Test avec différentes valeurs de montant
     */
    public function testMontantDemandeWithVariousValues(): void
    {
        // Test avec un petit montant
        $this->appelDeFonds->setMontantdemande('0.01');
        $this->assertSame('0.01', $this->appelDeFonds->getMontantdemande());
        
        // Test avec un montant moyen
        $this->appelDeFonds->setMontantdemande('1000.50');
        $this->assertSame('1000.50', $this->appelDeFonds->getMontantdemande());
        
        // Test avec un grand montant
        $this->appelDeFonds->setMontantdemande('9999999.99');
        $this->assertSame('9999999.99', $this->appelDeFonds->getMontantdemande());
    }

    /**
     * Test avec différentes dates
     */
    public function testDateDemandeWithVariousDates(): void
    {
        // Test avec une date passée
        $pastDate = new \DateTime('-1 year');
        $this->appelDeFonds->setDatedemande($pastDate);
        $this->assertSame($pastDate, $this->appelDeFonds->getDatedemande());
        
        // Test avec la date actuelle
        $currentDate = new \DateTime();
        $this->appelDeFonds->setDatedemande($currentDate);
        $this->assertSame($currentDate, $this->appelDeFonds->getDatedemande());
        
        // Test avec une date future
        $futureDate = new \DateTime('+1 month');
        $this->appelDeFonds->setDatedemande($futureDate);
        $this->assertSame($futureDate, $this->appelDeFonds->getDatedemande());
    }

    /**
     * Test de validation de l'association avec un projet
     */
    public function testProjetAssociation(): void
    {
        // Créer un autre projet
        $secondProjet = new Projet();
        $secondProjet->setProjetid(2);
        $secondProjet->setNom('Second Projet');
        
        // Associer au premier projet
        $this->appelDeFonds->setProjet($this->projet);
        $this->assertSame($this->projet, $this->appelDeFonds->getProjet());
        $this->assertSame('Projet Test', $this->appelDeFonds->getProjet()->getNom());
        
        // Changer pour le second projet
        $this->appelDeFonds->setProjet($secondProjet);
        $this->assertSame($secondProjet, $this->appelDeFonds->getProjet());
        $this->assertSame('Second Projet', $this->appelDeFonds->getProjet()->getNom());
    }
} 