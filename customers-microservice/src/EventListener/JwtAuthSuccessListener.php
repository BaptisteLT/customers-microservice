<?php
namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Prometheus\CollectorRegistry;

class JwtAuthSuccessListener
{
    private CollectorRegistry $registry;

    public function __construct(CollectorRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {
        $counter = $this->registry->getOrRegisterCounter('app', 'jwt_auth_success_total', 'Nombre de connexions JWT réussies');
        $counter->inc();
    }
}
