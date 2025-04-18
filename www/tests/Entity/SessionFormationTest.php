<?php

namespace App\Tests\Entity;

use App\Entity\SessionFormation;
use App\Entity\Projet;
use PHPUnit\Framework\TestCase;

class SessionFormationTest extends TestCase
{
    private SessionFormation $sessionFormation;
    private Projet $projet;

    protected function setUp(): void
    {
        $this->sessionFormation = new SessionFormation();
        $this->projet = new Projet();
        $this->projet->setProjetid(1);
        $this->projet->setNom('Projet Test');
    }

    public function testGetSetSessionId(): void
    {
        $this->sessionFormation->setSessionid(42);
        $this->assertSame(42, $this->sessionFormation->getSessionid());
    }

    public function testGetSetProjet(): void
    {
        $this->sessionFormation->setProjet($this->projet);
        $this->assertSame($this->projet, $this->sessionFormation->getProjet());
    }

    public function testGetSetDateDebut(): void
    {
        $dateDebut = new \DateTime('2023-01-15');
        $this->sessionFormation->setDatedebut($dateDebut);
        $this->assertSame($dateDebut, $this->sessionFormation->getDatedebut());
    }

    public function testGetSetDateFin(): void
    {
        $dateFin = new \DateTime('2023-02-15');
        $this->sessionFormation->setDatefin($dateFin);
        $this->assertSame($dateFin, $this->sessionFormation->getDatefin());
    }

    public function testGetSetCoutTotal(): void
    {
        $this->sessionFormation->setCouttotal('5000.00');
        $this->assertSame('5000.00', $this->sessionFormation->getCouttotal());
    }

    /**
     * Test cohérence des dates (début avant fin)
     */
    public function testDatesCoherence(): void
    {
        $dateDebut = new \DateTime('2023-01-15');
        $dateFin = new \DateTime('2023-02-15');
        
        $this->sessionFormation->setDatedebut($dateDebut);
        $this->sessionFormation->setDatefin($dateFin);
        
        // Vérifier que la date de début est bien avant la date de fin
        $this->assertTrue($this->sessionFormation->getDatedebut() < $this->sessionFormation->getDatefin());
    }

    /**
     * Test avec des valeurs extrêmes pour le coût total
     */
    public function testCoutTotalWithExtremeValues(): void
    {
        // Test avec un petit montant
        $this->sessionFormation->setCouttotal('0.01');
        $this->assertSame('0.01', $this->sessionFormation->getCouttotal());
        
        // Test avec un grand montant
        $this->sessionFormation->setCouttotal('9999999.99');
        $this->assertSame('9999999.99', $this->sessionFormation->getCouttotal());
    }

    /**
     * Test avec des dates extrêmes (passé lointain et futur lointain)
     */
    public function testExtremesDates(): void
    {
        // Test avec une date très ancienne
        $oldDate = new \DateTime('2000-01-01');
        $this->sessionFormation->setDatedebut($oldDate);
        $this->assertSame($oldDate, $this->sessionFormation->getDatedebut());
        
        // Test avec une date très future
        $farFutureDate = new \DateTime('2050-12-31');
        $this->sessionFormation->setDatefin($farFutureDate);
        $this->assertSame($farFutureDate, $this->sessionFormation->getDatefin());
    }

    /**
     * Test avec une même date de début et de fin
     */
    public function testSameDaySession(): void
    {
        $sameDay = new \DateTime('2023-06-15');
        
        $this->sessionFormation->setDatedebut($sameDay);
        $this->sessionFormation->setDatefin($sameDay);
        
        $this->assertEquals($this->sessionFormation->getDatedebut(), $this->sessionFormation->getDatefin());
    }
} 