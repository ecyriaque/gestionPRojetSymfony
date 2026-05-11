<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Restreint l'accès aux routes /api/ et /api/doc selon les plages IP autorisées.
 * Les plages sont définies via variables d'environnement serveur (pas dans .env).
 *
 * Variables attendues :
 *   API_ALLOWED_IPS   : IPs autorisées pour /api/v1  (ex: "1.2.3.4,5.6.7.8")
 *   SWAGGER_ALLOWED_CIDRS : CIDRs autorisés pour /api/doc (ex: "10.0.0.0/8,192.168.1.0/24")
 */
class ApiIpRestrictionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::REQUEST => ['onRequest', 10]];
    }

    public function onRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $path    = $request->getPathInfo();
        $ip      = $request->getClientIp() ?? '';

        // Restriction IP sur /api/doc (Swagger)
        if (str_starts_with($path, '/api/doc')) {
            $rawCidrs = $_SERVER['SWAGGER_ALLOWED_CIDRS'] ?? getenv('SWAGGER_ALLOWED_CIDRS');
            if ($rawCidrs) {
                $cidrs = array_filter(array_map('trim', explode(',', $rawCidrs)));
                if (!$this->ipMatchesAnyCidr($ip, $cidrs)) {
                    $event->setResponse(new Response('Accès interdit : IP non autorisée.', Response::HTTP_FORBIDDEN));
                    return;
                }
            }
        }

        // Restriction IP sur /api/v1 (API REST)
        if (str_starts_with($path, '/api/v1')) {
            $rawIps = $_SERVER['API_ALLOWED_IPS'] ?? getenv('API_ALLOWED_IPS');
            if ($rawIps) {
                $allowedIps = array_filter(array_map('trim', explode(',', $rawIps)));
                if (!in_array($ip, $allowedIps, true)) {
                    $event->setResponse(new JsonResponse(
                        ['error' => 'Forbidden', 'message' => 'IP non autorisée.'],
                        Response::HTTP_FORBIDDEN
                    ));
                    return;
                }
            }
        }
    }

    private function ipMatchesAnyCidr(string $ip, array $cidrs): bool
    {
        foreach ($cidrs as $cidr) {
            if ($this->ipInCidr($ip, $cidr)) {
                return true;
            }
        }
        return false;
    }

    private function ipInCidr(string $ip, string $cidr): bool
    {
        if (!str_contains($cidr, '/')) {
            return $ip === $cidr;
        }

        [$subnet, $bits] = explode('/', $cidr);
        $ipLong     = ip2long($ip);
        $subnetLong = ip2long($subnet);

        if ($ipLong === false || $subnetLong === false) {
            return false;
        }

        $mask = -1 << (32 - (int) $bits);
        return ($ipLong & $mask) === ($subnetLong & $mask);
    }
}
