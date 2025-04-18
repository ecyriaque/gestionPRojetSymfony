<?php

namespace App\Tests\Entity;

use App\Entity\Formation;
use App\Entity\SessionFormation;
use PHPUnit\Framework\TestCase;

class FormationTest extends TestCase
{
    private Formation $formation;
    private SessionFormation $session;

    protected function setUp(): void
    {
        $this->formation = new Formation();
        $this->session = new SessionFormation();
        $this->session->setSessionid(1);
    }

    public function testGetSetFormationId(): void
    {
        $this->formation->setFormationid(42);
        $this->assertSame(42, $this->formation->getFormationid());
    }

    public function testGetSetSession(): void
    {
        $this->formation->setSession($this->session);
        $this->assertSame($this->session, $this->formation->getSession());
    }

    public function testGetSetOrganisme(): void
    {
        $this->formation->setOrganisme('Centre de Formation ABC');
        $this->assertSame('Centre de Formation ABC', $this->formation->getOrganisme());
    }

    public function testGetSetCoutHT(): void
    {
        $this->formation->setCouht('1500.50');
        $this->assertSame('1500.50', $this->formation->getCouht());
    }

    public function testGetSetTauxTVA(): void
    {
        $this->formation->setTauxtva('20.00');
        $this->assertSame('20.00', $this->formation->getTauxtva());
    }

    public function testGetSetDateFormation(): void
    {
        $date = new \DateTime('2023-06-15');
        $this->formation->setDateformation($date);
        $this->assertSame($date, $this->formation->getDateformation());
    }

    /**
     * Test avec des valeurs limites pour couht
     */
    public function testCoutHTWithExtremesValues(): void
    {
        // Test avec un très petit montant
        $this->formation->setCouht('0.01');
        $this->assertSame('0.01', $this->formation->getCouht());
        
        // Test avec un très grand montant
        $this->formation->setCouht('9999999.99');
        $this->assertSame('9999999.99', $this->formation->getCouht());
    }

    /**
     * Test avec des valeurs limites pour tauxtva
     */
    public function testTauxTVAWithExtremesValues(): void
    {
        // Test avec un taux de 0%
        $this->formation->setTauxtva('0.00');
        $this->assertSame('0.00', $this->formation->getTauxtva());
        
        // Test avec un taux maximum
        $this->formation->setTauxtva('99.99');
        $this->assertSame('99.99', $this->formation->getTauxtva());
    }

    /**
     * Test date de formation passée et future
     */
    public function testDateFormationPastAndFuture(): void
    {
        // Test avec une date passée
        $pastDate = new \DateTime('-1 year');
        $this->formation->setDateformation($pastDate);
        $this->assertSame($pastDate, $this->formation->getDateformation());
        
        // Test avec une date future
        $futureDate = new \DateTime('+1 year');
        $this->formation->setDateformation($futureDate);
        $this->assertSame($futureDate, $this->formation->getDateformation());
    }
} 