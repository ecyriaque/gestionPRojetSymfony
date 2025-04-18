<?php

namespace App\Tests\Entity;

use App\Entity\Client;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    private Client $client;

    protected function setUp(): void
    {
        $this->client = new Client();
    }

    public function testGetSetClientId(): void
    {
        $this->client->setClientid(42);
        $this->assertSame(42, $this->client->getClientid());
    }

    public function testGetSetNom(): void
    {
        $this->client->setNom('Entreprise ABC');
        $this->assertSame('Entreprise ABC', $this->client->getNom());
    }

    public function testGetSetSiren(): void
    {
        $this->client->setSiren('123456789');
        $this->assertSame('123456789', $this->client->getSiren());
    }

    public function testGetSetIban(): void
    {
        $this->client->setIban('FR7630001007941234567890185');
        $this->assertSame('FR7630001007941234567890185', $this->client->getIban());
    }

    public function testGetSetAdresse(): void
    {
        $adresse = '123 rue Principale, 75000 Paris';
        $this->client->setAdresse($adresse);
        $this->assertSame($adresse, $this->client->getAdresse());
    }

    public function testGetSetCommission(): void
    {
        $this->client->setCommission('2.50');
        $this->assertSame('2.50', $this->client->getCommission());
    }

    public function testGetSetEmailContactFacturation(): void
    {
        $email = 'contact@entreprise-abc.com';
        $this->client->setEmailcontactfacturation($email);
        $this->assertSame($email, $this->client->getEmailcontactfacturation());
    }
} 