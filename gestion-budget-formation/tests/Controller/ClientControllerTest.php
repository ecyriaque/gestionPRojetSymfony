<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UtilisateurRepository;

class ClientControllerTest extends WebTestCase
{
    private $client;
    private $utilisateurRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->utilisateurRepository = static::getContainer()->get(UtilisateurRepository::class);
    }

    public function testIndexPageIsSecured(): void
    {
        // L'accès à la page index des clients sans être connecté doit rediriger vers la page de login
        $this->client->request('GET', '/client/');
        
        $this->assertResponseRedirects('/login');
    }

    public function testIndexPageAsAdmin(): void
    {
        // Connexion en tant qu'admin
        $testUser = $this->utilisateurRepository->findOneByEmail('admin@example.com');
        
        // Si l'utilisateur n'existe pas dans la base de test, on skip le test
        if (!$testUser) {
            $this->markTestSkipped('L\'utilisateur admin@example.com doit exister pour ce test.');
        }
        
        $this->client->loginUser($testUser);
        
        // Accès à la page index des clients
        $this->client->request('GET', '/client/');
        
        // Vérifier que la réponse est un succès
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Liste des Clients');
    }

    public function testNewClient(): void
    {
        // Connexion en tant qu'admin
        $testUser = $this->utilisateurRepository->findOneByEmail('admin@example.com');
        
        // Si l'utilisateur n'existe pas dans la base de test, on skip le test
        if (!$testUser) {
            $this->markTestSkipped('L\'utilisateur admin@example.com doit exister pour ce test.');
        }
        
        $this->client->loginUser($testUser);
        
        // Accès au formulaire de création de client
        $crawler = $this->client->request('GET', '/client/new');
        
        // Vérifier que la page contient un formulaire
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form[name="client"]');

        // Soumettre le formulaire avec des données
        $form = $crawler->selectButton('Enregistrer')->form([
            'client[nom]' => 'Client Test',
            'client[siren]' => '123456789',
            'client[iban]' => 'FR7630001007941234567890185',
            'client[adresse]' => '123 rue Principale, 75000 Paris',
            'client[commission]' => '2.50',
            'client[emailcontactfacturation]' => 'contact@client-test.com'
        ]);

        $this->client->submit($form);
        
        // Vérifier la redirection vers la liste des clients après la création
        $this->assertResponseRedirects('/client/');
        
        // Suivre la redirection
        $this->client->followRedirect();
        
        // Vérifier que le nouveau client apparaît dans la liste
        $this->assertSelectorTextContains('body', 'Client Test');
    }
} 