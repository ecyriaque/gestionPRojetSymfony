<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Si l'utilisateur est déjà connecté, on le redirige ailleurs
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        // Récupérer la dernière erreur de login (s'il y en a une)
        $error = $authenticationUtils->getLastAuthenticationError();

        // Récupérer le dernier email saisi par l'utilisateur
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error
        ]);
    }

    #[Route('/logout', name: 'logout')]
    public function logout(): void
    {
        // Ce chemin sera intercepté par la configuration de Symfony (firewall)
        // Le code ne sera jamais exécuté.
    }
}
