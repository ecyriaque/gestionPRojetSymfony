<?php

namespace App\EventSubscriber;

use App\Service\AppLogger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Subscriber pour mesurer et journaliser les performances des requêtes
 */
class PerformanceSubscriber implements EventSubscriberInterface
{
    private AppLogger $logger;
    private array $requestStartTimes = [];
    
    // Durée au-delà de laquelle on considère qu'une requête est lente (en secondes)
    private const SLOW_REQUEST_THRESHOLD = 1.0;

    public function __construct(AppLogger $logger)
    {
        $this->logger = $logger;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 9999], // Priorité élevée pour s'exécuter tôt
            KernelEvents::RESPONSE => ['onKernelResponse', -9999], // Priorité basse pour s'exécuter tard
        ];
    }

    /**
     * Enregistre le début de la requête
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $this->requestStartTimes[$this->getRequestIdentifier($request)] = microtime(true);
    }

    /**
     * Mesure la durée et journalise les requêtes lentes
     */
    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $requestIdentifier = $this->getRequestIdentifier($request);
        
        if (!isset($this->requestStartTimes[$requestIdentifier])) {
            return;
        }

        $startTime = $this->requestStartTimes[$requestIdentifier];
        $endTime = microtime(true);
        $duration = $endTime - $startTime;
        
        // Données de base pour tous les logs de performance
        $context = [
            'route' => $request->attributes->get('_route'),
            'uri' => $request->getRequestUri(),
            'method' => $request->getMethod(),
            'status_code' => $event->getResponse()->getStatusCode(),
            'duration' => round($duration, 4),
        ];
        
        // Toujours enregistrer la performance pour les statistiques
        $this->logger->logPerformance(
            'http_request',
            $duration,
            $context
        );
        
        // Journalise spécifiquement les requêtes lentes
        if ($duration > self::SLOW_REQUEST_THRESHOLD) {
            $this->logger->logPerformance(
                'slow_request',
                $duration,
                array_merge($context, [
                    'threshold' => self::SLOW_REQUEST_THRESHOLD,
                    'queries' => $this->getQueryCount()
                ]),
                'warning'
            );
        }
        
        // Nettoyage
        unset($this->requestStartTimes[$requestIdentifier]);
    }
    
    /**
     * Génère un identifiant unique pour la requête
     */
    private function getRequestIdentifier($request): string
    {
        return spl_object_hash($request);
    }
    
    /**
     * Récupère le nombre de requêtes SQL si Doctrine est disponible
     */
    private function getQueryCount(): ?int
    {
        // Ceci est un exemple - à implémenter si vous utilisez le Doctrine Profiler
        // Pour l'instant on retourne null
        return null;
    }
} 