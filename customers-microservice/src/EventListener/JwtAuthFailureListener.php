<?php
namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Prometheus\CollectorRegistry;

class JwtAuthFailureListener
{
    private CollectorRegistry $registry;

    public function __construct(CollectorRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function onAuthenticationFailure(AuthenticationFailureEvent $event): void
    {
        $counter = $this->registry->getOrRegisterCounter('app', 'jwt_auth_failure_total', 'Nombre d’échecs d’authentification JWT');
        $counter->inc();
    }
}
