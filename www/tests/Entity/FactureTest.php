<?php

namespace App\Tests\Entity;

use App\Entity\Facture;
use App\Entity\Projet;
use PHPUnit\Framework\TestCase;

class FactureTest extends TestCase
{
    private Facture $facture;
    private Projet $projet;

    protected function setUp(): void
    {
        $this->facture = new Facture();
        $this->projet = new Projet();
        $this->projet->setProjetid(1);
        $this->projet->setNom('Projet Test');
    }

    public function testGetSetFactureId(): void
    {
        $this->facture->setFactureid(42);
        $this->assertSame(42, $this->facture->getFactureid());
    }

    public function testGetSetProjet(): void
    {
        $this->facture->setProjet($this->projet);
        $this->assertSame($this->projet, $this->facture->getProjet());
    }

    public function testGetSetDateEmission(): void
    {
        $date = new \DateTime('2023-06-15');
        $this->facture->setDateemission($date);
        $this->assertSame($date, $this->facture->getDateemission());
    }

    public function testGetSetMontantTotal(): void
    {
        $this->facture->setMontanttotal('5000.00');
        $this->assertSame('5000.00', $this->facture->getMontanttotal());
    }

    public function testGetSetEtat(): void
    {
        // Test pour chaque état valide
        $etats = ['En attente', 'Payee', 'Annulee'];
        
        foreach ($etats as $etat) {
            $this->facture->setEtat($etat);
            $this->assertSame($etat, $this->facture->getEtat());
        }
    }

    /**
     * Test avec différentes valeurs de montant
     */
    public function testMontantTotalWithVariousValues(): void
    {
        // Test avec un petit montant
        $this->facture->setMontanttotal('0.01');
        $this->assertSame('0.01', $this->facture->getMontanttotal());
        
        // Test avec un montant moyen
        $this->facture->setMontanttotal('1000.50');
        $this->assertSame('1000.50', $this->facture->getMontanttotal());
        
        // Test avec un grand montant
        $this->facture->setMontanttotal('9999999.99');
        $this->assertSame('9999999.99', $this->facture->getMontanttotal());
    }

    /**
     * Test avec différentes dates d'émission
     */
    public function testDateEmissionWithVariousDates(): void
    {
        // Test avec une date passée
        $pastDate = new \DateTime('-1 year');
        $this->facture->setDateemission($pastDate);
        $this->assertSame($pastDate, $this->facture->getDateemission());
        
        // Test avec la date actuelle
        $currentDate = new \DateTime();
        $this->facture->setDateemission($currentDate);
        $this->assertSame($currentDate, $this->facture->getDateemission());
        
        // Test avec une date future (moins courant pour une facture, mais possible)
        $futureDate = new \DateTime('+1 month');
        $this->facture->setDateemission($futureDate);
        $this->assertSame($futureDate, $this->facture->getDateemission());
    }

    /**
     * Test des transitions d'état de la facture
     */
    public function testEtatTransitions(): void
    {
        // Initial state
        $this->facture->setEtat('En attente');
        $this->assertSame('En attente', $this->facture->getEtat());
        
        // Transition to paid
        $this->facture->setEtat('Payee');
        $this->assertSame('Payee', $this->facture->getEtat());
        
        // Transition to canceled
        $this->facture->setEtat('Annulee');
        $this->assertSame('Annulee', $this->facture->getEtat());
        
        // Test de transition de l'état annulé vers en attente
        $this->facture->setEtat('En attente');
        $this->assertSame('En attente', $this->facture->getEtat());
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
        $this->facture->setProjet($this->projet);
        $this->assertSame($this->projet, $this->facture->getProjet());
        $this->assertSame('Projet Test', $this->facture->getProjet()->getNom());
        
        // Changer pour le second projet
        $this->facture->setProjet($secondProjet);
        $this->assertSame($secondProjet, $this->facture->getProjet());
        $this->assertSame('Second Projet', $this->facture->getProjet()->getNom());
    }
} 