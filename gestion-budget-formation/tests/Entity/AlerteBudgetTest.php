<?php

namespace App\Tests\Entity;

use App\Entity\AlerteBudget;
use App\Entity\Projet;
use PHPUnit\Framework\TestCase;

class AlerteBudgetTest extends TestCase
{
    private AlerteBudget $alerteBudget;
    private Projet $projet;

    protected function setUp(): void
    {
        $this->alerteBudget = new AlerteBudget();
        $this->projet = new Projet();
        $this->projet->setProjetid(1);
        $this->projet->setNom('Projet Test');
        $this->projet->setBudgetinitial('10000.00');
        $this->projet->setSeuilalerte('9000.00');
    }

    public function testGetSetAlerteId(): void
    {
        $this->alerteBudget->setAlerteid(42);
        $this->assertSame(42, $this->alerteBudget->getAlerteid());
    }

    public function testGetSetProjet(): void
    {
        $this->alerteBudget->setProjet($this->projet);
        $this->assertSame($this->projet, $this->alerteBudget->getProjet());
    }

    public function testGetSetMontantDepasse(): void
    {
        $this->alerteBudget->setMontantdepasse('500.00');
        $this->assertSame('500.00', $this->alerteBudget->getMontantdepasse());
    }

    public function testGetSetDateAlerte(): void
    {
        $date = new \DateTime('2023-06-15');
        $this->alerteBudget->setDatealerte($date);
        $this->assertSame($date, $this->alerteBudget->getDatealerte());
    }

    /**
     * Test avec différentes valeurs de montant dépassé
     */
    public function testMontantDepasseWithVariousValues(): void
    {
        // Test avec un petit montant
        $this->alerteBudget->setMontantdepasse('0.01');
        $this->assertSame('0.01', $this->alerteBudget->getMontantdepasse());
        
        // Test avec un montant moyen
        $this->alerteBudget->setMontantdepasse('1000.50');
        $this->assertSame('1000.50', $this->alerteBudget->getMontantdepasse());
        
        // Test avec un grand montant
        $this->alerteBudget->setMontantdepasse('9999999.99');
        $this->assertSame('9999999.99', $this->alerteBudget->getMontantdepasse());
    }

    /**
     * Test avec différentes dates d'alerte
     */
    public function testDateAlerteWithVariousDates(): void
    {
        // Test avec une date passée
        $pastDate = new \DateTime('-1 year');
        $this->alerteBudget->setDatealerte($pastDate);
        $this->assertSame($pastDate, $this->alerteBudget->getDatealerte());
        
        // Test avec la date actuelle
        $currentDate = new \DateTime();
        $this->alerteBudget->setDatealerte($currentDate);
        $this->assertSame($currentDate, $this->alerteBudget->getDatealerte());
        
        // Test avec une date future
        $futureDate = new \DateTime('+1 day');
        $this->alerteBudget->setDatealerte($futureDate);
        $this->assertSame($futureDate, $this->alerteBudget->getDatealerte());
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
        $secondProjet->setBudgetinitial('20000.00');
        $secondProjet->setSeuilalerte('18000.00');
        
        // Associer au premier projet
        $this->alerteBudget->setProjet($this->projet);
        $this->alerteBudget->setMontantdepasse('500.00');
        $this->assertSame($this->projet, $this->alerteBudget->getProjet());
        $this->assertSame('Projet Test', $this->alerteBudget->getProjet()->getNom());
        $this->assertSame('10000.00', $this->alerteBudget->getProjet()->getBudgetinitial());
        $this->assertSame('500.00', $this->alerteBudget->getMontantdepasse());
        
        // Changer pour le second projet
        $this->alerteBudget->setProjet($secondProjet);
        $this->alerteBudget->setMontantdepasse('1000.00');
        $this->assertSame($secondProjet, $this->alerteBudget->getProjet());
        $this->assertSame('Second Projet', $this->alerteBudget->getProjet()->getNom());
        $this->assertSame('20000.00', $this->alerteBudget->getProjet()->getBudgetinitial());
        $this->assertSame('1000.00', $this->alerteBudget->getMontantdepasse());
    }

    /**
     * Test de vérification que le montant dépassé est cohérent avec le seuil d'alerte du projet
     */
    public function testCoherenceMontantDépasséAvecSeuil(): void
    {
        $this->alerteBudget->setProjet($this->projet);
        $this->alerteBudget->setMontantdepasse('1000.00');
        
        // Le projet a un seuil d'alerte de 9000.00, nous vérifions que le montant dépassé est inférieur au budget initial
        $budgetInitial = (float) $this->alerteBudget->getProjet()->getBudgetinitial();
        $montantDepasse = (float) $this->alerteBudget->getMontantdepasse();
        
        $this->assertLessThan($budgetInitial, $montantDepasse);
    }
} 