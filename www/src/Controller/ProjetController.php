<?php

namespace App\Controller;

use App\Entity\Projet;
use App\Form\ProjetType;
use App\Repository\ProjetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Psr\Log\LoggerInterface;

#[Route('/projet')]
final class ProjetController extends AbstractController
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    
    #[Route(name: 'app_projet_index', methods: ['GET'])]
    public function index(ProjetRepository $projetRepository): Response
    {
        $this->logger->info('Liste des projets consultée', [
            'user' => $this->getUser() ? $this->getUser()->getUserIdentifier() : 'anonyme',
            'action' => 'list_projets'
        ]);
        
        return $this->render('projet/index.html.twig', [
            'projets' => $projetRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_projet_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $projet = new Projet();
        $form = $this->createForm(ProjetType::class, $projet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($projet);
            $entityManager->flush();

            $this->logger->info('Nouveau projet créé', [
                'user' => $this->getUser() ? $this->getUser()->getUserIdentifier() : 'anonyme',
                'action' => 'create_projet',
                'projet_id' => $projet->getProjetid(),
                'projet_nom' => $projet->getNom()
            ]);

            return $this->redirectToRoute('app_projet_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('projet/new.html.twig', [
            'projet' => $projet,
            'form' => $form,
        ]);
    }

    #[Route('/{projetid}', name: 'app_projet_show', methods: ['GET'])]
    public function show(Projet $projet): Response
    {
        $this->logger->info('Détails du projet consultés', [
            'user' => $this->getUser() ? $this->getUser()->getUserIdentifier() : 'anonyme',
            'action' => 'view_projet',
            'projet_id' => $projet->getProjetid(),
            'projet_nom' => $projet->getNom()
        ]);
        
        return $this->render('projet/show.html.twig', [
            'projet' => $projet,
        ]);
    }

    #[Route('/{projetid}/edit', name: 'app_projet_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Projet $projet, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProjetType::class, $projet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->logger->info('Projet modifié', [
                'user' => $this->getUser() ? $this->getUser()->getUserIdentifier() : 'anonyme',
                'action' => 'edit_projet',
                'projet_id' => $projet->getProjetid(),
                'projet_nom' => $projet->getNom()
            ]);

            return $this->redirectToRoute('app_projet_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('projet/edit.html.twig', [
            'projet' => $projet,
            'form' => $form,
        ]);
    }

    #[Route('/{projetid}', name: 'app_projet_delete', methods: ['POST'])]
    public function delete(Request $request, Projet $projet, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$projet->getProjetid(), $request->getPayload()->getString('_token'))) {
            // Log avant la suppression pour garder l'information
            $this->logger->warning('Projet supprimé', [
                'user' => $this->getUser() ? $this->getUser()->getUserIdentifier() : 'anonyme',
                'action' => 'delete_projet',
                'projet_id' => $projet->getProjetid(),
                'projet_nom' => $projet->getNom()
            ]);
            
            $entityManager->remove($projet);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_projet_index', [], Response::HTTP_SEE_OTHER);
    }
}
