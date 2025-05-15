<?php

// src/Controller/UtilisateurController.php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Psr\Log\LoggerInterface;

#[Route('/utilisateur')]
final class UtilisateurController extends AbstractController
{
    private $passwordHasher;
    private LoggerInterface $logger;

    public function __construct(UserPasswordHasherInterface $passwordHasher, LoggerInterface $logger)
    {
        $this->passwordHasher = $passwordHasher;
        $this->logger = $logger;
    }

    #[Route(name: 'app_utilisateur_index', methods: ['GET'])]
    public function index(UtilisateurRepository $utilisateurRepository): Response
    {
        $this->logger->info('Liste des utilisateurs consultée', [
            'user' => $this->getUser() ? $this->getUser()->getUserIdentifier() : 'anonyme',
            'action' => 'list_users',
            'module' => 'security'
        ]);
        
        return $this->render('utilisateur/index.html.twig', [
            'utilisateurs' => $utilisateurRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_utilisateur_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $utilisateur = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hacher le mot de passe si un mot de passe est soumis
            $password = $utilisateur->getMotdepasse();
            if ($password) {
                $hashedPassword = $this->passwordHasher->hashPassword($utilisateur, $password);
                $utilisateur->setMotdepasse($hashedPassword);
            }

            $entityManager->persist($utilisateur);
            $entityManager->flush();

            $this->logger->notice('Nouvel utilisateur créé', [
                'user_admin' => $this->getUser() ? $this->getUser()->getUserIdentifier() : 'anonyme',
                'created_user' => $utilisateur->getEmail(),
                'user_role' => $utilisateur->getRole(),
                'action' => 'create_user',
                'module' => 'security',
                'ip' => $request->getClientIp()
            ]);

            return $this->redirectToRoute('app_utilisateur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('utilisateur/new.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form,
        ]);
    }

    #[Route('/{utilisateurid}', name: 'app_utilisateur_show', methods: ['GET'])]
    public function show(Utilisateur $utilisateur): Response
    {
        $this->logger->info('Profil utilisateur consulté', [
            'user' => $this->getUser() ? $this->getUser()->getUserIdentifier() : 'anonyme',
            'viewed_user' => $utilisateur->getEmail(),
            'action' => 'view_user',
            'module' => 'security'
        ]);
        
        return $this->render('utilisateur/show.html.twig', [
            'utilisateur' => $utilisateur,
        ]);
    }

    #[Route('/{utilisateurid}/edit', name: 'app_utilisateur_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Utilisateur $utilisateur, EntityManagerInterface $entityManager): Response
    {
        $originalRole = $utilisateur->getRole();
        $originalEmail = $utilisateur->getEmail();
        $passwordChanged = false;
        
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hacher le mot de passe si il a été modifié
            $password = $utilisateur->getMotdepasse();
            if ($password) {
                $hashedPassword = $this->passwordHasher->hashPassword($utilisateur, $password);
                $utilisateur->setMotdepasse($hashedPassword);
                $passwordChanged = true;
            }

            $entityManager->flush();

            $this->logger->notice('Utilisateur modifié', [
                'user_admin' => $this->getUser() ? $this->getUser()->getUserIdentifier() : 'anonyme',
                'modified_user' => $utilisateur->getEmail(),
                'password_changed' => $passwordChanged,
                'role_changed' => $originalRole !== $utilisateur->getRole(),
                'email_changed' => $originalEmail !== $utilisateur->getEmail(),
                'action' => 'edit_user',
                'module' => 'security',
                'ip' => $request->getClientIp()
            ]);

            return $this->redirectToRoute('app_utilisateur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('utilisateur/edit.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form,
        ]);
    }

    #[Route('/{utilisateurid}', name: 'app_utilisateur_delete', methods: ['POST'])]
    public function delete(Request $request, Utilisateur $utilisateur, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$utilisateur->getUtilisateurid(), $request->request->get('_token'))) {
            // Log avant suppression
            $this->logger->alert('Utilisateur supprimé', [
                'user_admin' => $this->getUser() ? $this->getUser()->getUserIdentifier() : 'anonyme',
                'deleted_user' => $utilisateur->getEmail(),
                'deleted_user_role' => $utilisateur->getRole(),
                'action' => 'delete_user',
                'module' => 'security',
                'ip' => $request->getClientIp()
            ]);
            
            $entityManager->remove($utilisateur);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_utilisateur_index', [], Response::HTTP_SEE_OTHER);
    }
}