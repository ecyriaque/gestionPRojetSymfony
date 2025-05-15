<?php

namespace App\Twig;

use App\Service\AppLogger;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Extension Twig pour permettre la journalisation depuis les templates
 */
class LoggerExtension extends AbstractExtension
{
    private AppLogger $appLogger;

    public function __construct(AppLogger $appLogger)
    {
        $this->appLogger = $appLogger;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('log_action', [$this, 'logAction']),
            new TwigFunction('log_business', [$this, 'logBusiness']),
            new TwigFunction('log_security', [$this, 'logSecurity']),
        ];
    }

    /**
     * Fonction Twig pour journaliser une action utilisateur
     */
    public function logAction(string $action, string $message, array $context = [], string $level = 'info'): void
    {
        $this->appLogger->logUserAction($action, $message, $context, $level);
    }

    /**
     * Fonction Twig pour journaliser un événement métier
     */
    public function logBusiness(string $entity, string $action, string $message, array $context = [], string $level = 'info'): void
    {
        $this->appLogger->logBusiness($entity, $action, $message, $context, $level);
    }

    /**
     * Fonction Twig pour journaliser un événement de sécurité
     */
    public function logSecurity(string $action, string $message, array $context = [], string $level = 'notice'): void
    {
        $this->appLogger->logSecurity($action, $message, $context, $level);
    }
} 