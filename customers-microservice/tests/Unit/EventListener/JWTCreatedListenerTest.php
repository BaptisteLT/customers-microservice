<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Customer;
use App\EventListener\JWTCreatedListener;
use App\Repository\CustomerRepository;
use DateTimeImmutable;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;
use function PHPUnit\Framework\assertContains;

class JWTCreatedListenerTest extends KernelTestCase
{

    private Customer $customer;

    private ContainerInterface $container;

    protected function setUp(): void
    {
        $this->customer = new Customer();
        $this->customer->setUuid(Uuid::v4());
        self::bootKernel();
        $this->container = static::getContainer();

    }


    public function testUuidIsAddedToPayload()
    {
        $requestStack = $this->createMock(RequestStack::class);
        $customerRepository = $this->createMock(CustomerRepository::class);
        $customerRepository->method("findOneBy")->willReturn($this->customer);

        $listener = new JWTCreatedListener($requestStack, $customerRepository);

        $event = $this->createMock(JWTCreatedEvent::class);
        $event->expects(self::once())->method("getData")->willReturn([
            'username' => 'test',
        ]);

        // On défini le payload qui est attendu en retour
        $expectedPayload = [
            'username' => 'test',
            'uuid' => $this->customer->getUuid(),
        ];

        $event->expects(self::once())
        ->method("setData")
        ->with($expectedPayload);

        $listener->onJWTCreated($event);

        $this->assertSame(['lexik_jwt_authentication.on_jwt_created' => 'onJWTCreated'], $listener::getSubscribedEvents());
    }

}