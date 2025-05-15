<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * Service de logs personnalisé qui ajoute automatiquement des informations contextuelles
 */
class AppLogger
{
    private LoggerInterface $logger;
    private RequestStack $requestStack;
    private Security $security;
    private ?string $appVersion;

    public function __construct(
        LoggerInterface $logger,
        RequestStack $requestStack,
        Security $security,
        string $appVersion = null
    ) {
        $this->logger = $logger;
        $this->requestStack = $requestStack;
        $this->security = $security;
        $this->appVersion = $appVersion ?? 'dev';
    }

    /**
     * Journalise une action utilisateur
     */
    public function logUserAction(string $action, string $message, array $context = [], string $level = 'info'): void
    {
        $request = $this->requestStack->getCurrentRequest();
        $user = $this->security->getUser();

        $enrichedContext = array_merge($context, [
            'action' => $action,
            'user' => $user ? $user->getUserIdentifier() : 'anonyme',
            'ip' => $request ? $request->getClientIp() : 'unknown',
            'method' => $request ? $request->getMethod() : 'unknown',
            'route' => $request && $request->attributes->get('_route') ? $request->attributes->get('_route') : 'unknown',
            'app_version' => $this->appVersion,
            'timestamp' => new \DateTime()
        ]);

        switch ($level) {
            case 'debug':
                $this->logger->debug($message, $enrichedContext);
                break;
            case 'info':
                $this->logger->info($message, $enrichedContext);
                break;
            case 'notice':
                $this->logger->notice($message, $enrichedContext);
                break;
            case 'warning':
                $this->logger->warning($message, $enrichedContext);
                break;
            case 'error':
                $this->logger->error($message, $enrichedContext);
                break;
            case 'critical':
                $this->logger->critical($message, $enrichedContext);
                break;
            case 'alert':
                $this->logger->alert($message, $enrichedContext);
                break;
            case 'emergency':
                $this->logger->emergency($message, $enrichedContext);
                break;
            default:
                $this->logger->info($message, $enrichedContext);
        }
    }

    /**
     * Journalise un événement de sécurité
     */
    public function logSecurity(string $action, string $message, array $context = [], string $level = 'notice'): void
    {
        $enrichedContext = array_merge($context, [
            'module' => 'security',
            'action' => $action
        ]);

        $this->logUserAction($action, $message, $enrichedContext, $level);
    }

    /**
     * Journalise un événement métier
     */
    public function logBusiness(string $entity, string $action, string $message, array $context = [], string $level = 'info'): void
    {
        $enrichedContext = array_merge($context, [
            'module' => 'business',
            'entity' => $entity,
            'action' => $action
        ]);

        $this->logUserAction($action, $message, $enrichedContext, $level);
    }

    /**
     * Journalise une erreur technique
     */
    public function logError(\Throwable $exception, string $message = null, array $context = []): void
    {
        $message = $message ?? 'Une erreur est survenue: ' . $exception->getMessage();
        
        $enrichedContext = array_merge($context, [
            'module' => 'error',
            'exception_class' => get_class($exception),
            'exception_message' => $exception->getMessage(),
            'exception_code' => $exception->getCode(),
            'exception_file' => $exception->getFile(),
            'exception_line' => $exception->getLine(),
            'exception_trace' => $exception->getTraceAsString()
        ]);

        $this->logUserAction('error', $message, $enrichedContext, 'error');
    }

    /**
     * Journalise les performances d'une opération
     */
    public function logPerformance(string $operation, float $duration, array $context = []): void
    {
        $enrichedContext = array_merge($context, [
            'module' => 'performance',
            'operation' => $operation,
            'duration' => $duration
        ]);

        $this->logUserAction('performance', "Performance de l'opération: {$operation}", $enrichedContext, 'info');
    }
} 