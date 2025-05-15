<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientType;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Psr\Log\LoggerInterface;

#[Route('/client')]
final class ClientController extends AbstractController
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    
    #[Route(name: 'app_client_index', methods: ['GET'])]
    public function index(ClientRepository $clientRepository): Response
    {
        $this->logger->info('Liste des clients consultée', [
            'user' => $this->getUser() ? $this->getUser()->getUserIdentifier() : 'anonyme',
            'action' => 'list_clients',
            'module' => 'client'
        ]);
        
        return $this->render('client/index.html.twig', [
            'clients' => $clientRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_client_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $client = new Client();
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($client);
            $entityManager->flush();

            $this->logger->info('Nouveau client créé', [
                'user' => $this->getUser() ? $this->getUser()->getUserIdentifier() : 'anonyme',
                'action' => 'create_client',
                'module' => 'client',
                'client_id' => $client->getClientid(),
                'client_nom' => $client->getNom(),
                'client_commission' => $client->getCommission()
            ]);

            return $this->redirectToRoute('app_client_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('client/new.html.twig', [
            'client' => $client,
            'form' => $form,
        ]);
    }

    #[Route('/{clientid}', name: 'app_client_show', methods: ['GET'])]
    public function show(Client $client): Response
    {
        $this->logger->info('Détails du client consultés', [
            'user' => $this->getUser() ? $this->getUser()->getUserIdentifier() : 'anonyme',
            'action' => 'view_client',
            'module' => 'client',
            'client_id' => $client->getClientid(),
            'client_nom' => $client->getNom()
        ]);
        
        return $this->render('client/show.html.twig', [
            'client' => $client,
        ]);
    }

    #[Route('/{clientid}/edit', name: 'app_client_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Client $client, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->logger->info('Client modifié', [
                'user' => $this->getUser() ? $this->getUser()->getUserIdentifier() : 'anonyme',
                'action' => 'edit_client',
                'module' => 'client',
                'client_id' => $client->getClientid(),
                'client_nom' => $client->getNom(),
                'client_commission' => $client->getCommission()
            ]);

            return $this->redirectToRoute('app_client_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('client/edit.html.twig', [
            'client' => $client,
            'form' => $form,
        ]);
    }

    #[Route('/{clientid}', name: 'app_client_delete', methods: ['POST'])]
    public function delete(Request $request, Client $client, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$client->getClientid(), $request->getPayload()->getString('_token'))) {
            // Log avant suppression
            $this->logger->warning('Client supprimé', [
                'user' => $this->getUser() ? $this->getUser()->getUserIdentifier() : 'anonyme',
                'action' => 'delete_client',
                'module' => 'client',
                'client_id' => $client->getClientid(),
                'client_nom' => $client->getNom()
            ]);
            
            $entityManager->remove($client);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_client_index', [], Response::HTTP_SEE_OTHER);
    }
}
