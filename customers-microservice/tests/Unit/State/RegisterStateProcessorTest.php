<?php

namespace App\Tests\Unit\Entity;

use ApiPlatform\Metadata\GraphQl\Operation;
use App\Entity\Customer;
use App\EventListener\JWTCreatedListener;
use App\Repository\CustomerRepository;
use App\State\RegisterStateProcessor;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;
use function PHPUnit\Framework\assertContains;

class RegisterStateProcessorTest extends KernelTestCase
{

    protected function setUp(): void
    {


    }
    public function testReturnsDataUnchangedIfNotCustomer(): void
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $hasher = $this->createMock(UserPasswordHasherInterface::class);

        $processor = new RegisterStateProcessor($em, $hasher);

        $nonCustomerData = new \stdClass(); // Anything that is not a Customer

        $result = $processor->process($nonCustomerData, $this->createMock(Operation::class));

        $this->assertSame($nonCustomerData, $result);
    }


    public function testThrowsConflictWhenUsernameExists(): void
    {
        $existingCustomer = new Customer();
        $newCustomer = new Customer();
        $newCustomer->setUsername('duplicate_user');

        // Mock the repository to return an existing user
        $repository = $this->createMock(CustomerRepository::class);
        $repository->method('findOneBy')
            ->with(['username' => 'duplicate_user'])
            ->willReturn($existingCustomer);

        // Mock the entity manager to return the mock repository
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method('getRepository')
            ->with(Customer::class)
            ->willReturn($repository);

        $passwordHasher = $this->createMock(UserPasswordHasherInterface::class);

        $processor = new RegisterStateProcessor($entityManager, $passwordHasher);

        $this->expectException(ConflictHttpException::class);
        $this->expectExceptionMessage('Username "duplicate_user" existe déjà.');

        $processor->process($newCustomer, $this->createMock(Operation::class));
    }
}