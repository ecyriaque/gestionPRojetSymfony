<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(LoggerInterface $logger): Response
    {
        // Log de test pour Logstash
        $logger->info('Test de connexion à Logstash', [
            'user' => 'admin',
            'action' => 'test_logstash',
            'timestamp' => new \DateTime()
        ]);
        
        return $this->render('home/index.html.twig');
    }
}
