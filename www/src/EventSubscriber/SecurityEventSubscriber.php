<?php

namespace App\EventSubscriber;

use App\Service\AppLogger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * Subscriber pour journaliser automatiquement les événements de sécurité
 */
class SecurityEventSubscriber implements EventSubscriberInterface
{
    private AppLogger $logger;
    private Security $security;

    public function __construct(AppLogger $logger, Security $security)
    {
        $this->logger = $logger;
        $this->security = $security;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => 'onLoginSuccess',
            LoginFailureEvent::class => 'onLoginFailure',
            LogoutEvent::class => 'onLogout',
            RequestEvent::class => 'onRequest',
            KernelEvents::EXCEPTION => 'onException',
        ];
    }

    /**
     * Journalise les connexions réussies
     */
    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();
        $request = $event->getRequest();
        
        $this->logger->logSecurity(
            'login_success',
            'Connexion réussie',
            [
                'user' => method_exists($user, 'getUserIdentifier') ? $user->getUserIdentifier() : (string) $user,
                'ip' => $request->getClientIp(),
                'user_agent' => $request->headers->get('User-Agent'),
            ]
        );
    }

    /**
     * Journalise les tentatives de connexion échouées
     */
    public function onLoginFailure(LoginFailureEvent $event): void
    {
        $exception = $event->getException();
        $request = $event->getRequest();
        
        // On récupère l'identifiant qui a été utilisé lors de la tentative
        $credentials = $request->request->all();
        $username = $credentials['_username'] ?? $credentials['email'] ?? 'unknown';
        
        $this->logger->logSecurity(
            'login_failure',
            'Échec de connexion',
            [
                'attempted_username' => $username,
                'ip' => $request->getClientIp(),
                'user_agent' => $request->headers->get('User-Agent'),
                'error' => $exception->getMessage()
            ],
            'warning'
        );
    }

    /**
     * Journalise les déconnexions
     */
    public function onLogout(LogoutEvent $event): void
    {
        $request = $event->getRequest();
        $token = $event->getToken();
        $user = $token ? $token->getUser() : 'unknown';
        
        $this->logger->logSecurity(
            'logout',
            'Déconnexion',
            [
                'user' => method_exists($user, 'getUserIdentifier') ? $user->getUserIdentifier() : (string) $user,
                'ip' => $request->getClientIp(),
                'user_agent' => $request->headers->get('User-Agent')
            ]
        );
    }

    /**
     * Journalise les requêtes importantes
     */
    public function onRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $route = $request->attributes->get('_route');
        
        // On ne journalise que les routes importantes pour éviter trop de logs
        $importantRoutes = [
            'app_client_new', 'app_client_delete',
            'app_projet_new', 'app_projet_delete',
            'app_utilisateur_new', 'app_utilisateur_delete',
        ];
        
        if (in_array($route, $importantRoutes)) {
            $this->logger->logUserAction(
                'access_route',
                'Accès à une route importante',
                [
                    'route' => $route,
                    'method' => $request->getMethod(),
                    'uri' => $request->getRequestUri(),
                ]
            );
        }
    }

    /**
     * Journalise les exceptions
     */
    public function onException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $request = $event->getRequest();
        
        $this->logger->logError(
            $exception,
            'Exception détectée',
            [
                'route' => $request->attributes->get('_route'),
                'uri' => $request->getRequestUri(),
                'method' => $request->getMethod()
            ]
        );
    }
} 