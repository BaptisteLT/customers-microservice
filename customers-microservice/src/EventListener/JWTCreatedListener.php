<?php
namespace App\EventListener;

use App\Repository\CustomerRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class JWTCreatedListener implements EventSubscriberInterface
{
    private $requestStack;

    private CustomerRepository $customerRepository;


    public function __construct(RequestStack $requestStack, CustomerRepository $customerRepository)
    {
        $this->requestStack = $requestStack;
        $this->customerRepository = $customerRepository;
    }

    # Permet d'ajouter le UUID dans le payload du token JWT afin de servir de base d'identification entre les micro-services
    # Le champ username et id pouvant à terme changer, utiliser un UUID est bien plus fiable.
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        $payload = $event->getData();

        $payload['uuid'] = $this->customerRepository->findOneBy(['username' => $payload['username']])->getUuid();
 
        $event->setData($payload);
    }

    public static function getSubscribedEvents()
    {
        return [
            'lexik_jwt_authentication.on_jwt_created' => 'onJWTCreated',
        ];
    }
}