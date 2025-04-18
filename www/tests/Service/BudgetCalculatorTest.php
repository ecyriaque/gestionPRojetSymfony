<?php

namespace App\Tests\Service;

use App\Entity\Formation;
use App\Entity\Projet;
use App\Entity\SessionFormation;
use App\Service\BudgetCalculator;
use PHPUnit\Framework\TestCase;

class BudgetCalculatorTest extends TestCase
{
    private BudgetCalculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = new BudgetCalculator();
    }

    public function testCalculateFormationTTC(): void
    {
        // Création d'une formation de test
        $formation = new Formation();
        $formation->setCouht('1000.00');
        $formation->setTauxtva('20.00');

        // Calcul du montant TTC (1000 + 20% = 1200)
        $ttc = $this->calculator->calculateFormationTTC($formation);

        // Vérification du résultat
        $this->assertEquals(1200.0, $ttc);
    }

    public function testCalculateSessionTotal(): void
    {
        // Création d'une session de formation
        $session = $this->createMock(SessionFormation::class);
        $session->method('getSessionid')->willReturn(1);
        
        // Création de formations de test
        $formation1 = $this->createMock(Formation::class);
        $formation1->method('getCouht')->willReturn('1000.00');
        $formation1->method('getTauxtva')->willReturn('20.00');
        $formation1->method('getSession')->willReturn($session);
        
        $formation2 = $this->createMock(Formation::class);
        $formation2->method('getCouht')->willReturn('500.00');
        $formation2->method('getTauxtva')->willReturn('10.00');
        $formation2->method('getSession')->willReturn($session);

        // Calcul du total de la session (1000 * 1.2 + 500 * 1.1 = 1750)
        $total = $this->calculator->calculateSessionTotal($session, [$formation1, $formation2]);

        // Vérification du résultat
        $this->assertEquals(1750.0, $total, '', 0.01);
    }

    public function testCalculateBudgetUsagePercentage(): void
    {
        // Création d'un projet avec un budget initial
        $projet = new Projet();
        $projet->setBudgetinitial('10000.00');

        // Test avec différents montants utilisés
        // 5000 / 10000 * 100 = 50%
        $this->assertEquals(50.0, $this->calculator->calculateBudgetUsagePercentage($projet, 5000.0));
        
        // 0 / 10000 * 100 = 0%
        $this->assertEquals(0.0, $this->calculator->calculateBudgetUsagePercentage($projet, 0.0));
        
        // 15000 / 10000 * 100 = 150%
        $this->assertEquals(150.0, $this->calculator->calculateBudgetUsagePercentage($projet, 15000.0));
    }
    
    public function testCalculateBudgetUsagePercentageWithZeroBudget(): void
    {
        // Création d'un projet avec un budget initial de zéro
        $projet = new Projet();
        $projet->setBudgetinitial('0.00');

        // Test pour éviter la division par zéro
        $this->assertEquals(0.0, $this->calculator->calculateBudgetUsagePercentage($projet, 5000.0));
    }

    public function testIsAlertThresholdReached(): void
    {
        // Création d'un projet avec un seuil d'alerte
        $projet = new Projet();
        $projet->setSeuilalerte('5000.00');

        // Test avec différents montants utilisés
        $this->assertFalse($this->calculator->isAlertThresholdReached($projet, 4999.99));
        $this->assertTrue($this->calculator->isAlertThresholdReached($projet, 5000.0));
        $this->assertTrue($this->calculator->isAlertThresholdReached($projet, 6000.0));
    }
} 