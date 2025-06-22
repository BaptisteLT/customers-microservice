<?php

namespace App\Tests\Integration\Entity;

use App\Entity\Customer;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

class CustomerTest extends KernelTestCase
{
    private Customer $customer;

    private ContainerInterface $container;

    private EntityManagerInterface $em;
    private UserPasswordHasherInterface $passwordHasher;


    protected function setUp(): void
    {
        self::bootKernel();
        $this->em = static::getContainer()->get(EntityManagerInterface::class);
        $this->passwordHasher = static::getContainer()->get(UserPasswordHasherInterface::class);

    }


    //TODO test exceptions when username, etc is null in the db
    public function testCustomerPersistsToDatabase(): void
    {
        $customer = new Customer();
        $customer->setUsername('IntegrationUser');
        $customer->setFirstName('John');
        $customer->setLastName('Doe');
        $customer->setCity('Paris');
        $customer->setPostalCode('75000');
        $customer->setCompanyName('ACME Inc');
        $customer->setRoles(['ROLE_USER']);

        $plainPassword = 'Valid1!Password';
        $customer->setPlainPassword($plainPassword);
        $hashedPassword = $this->passwordHasher->hashPassword($customer, $plainPassword);
        $customer->setPassword($hashedPassword);

        $this->em->persist($customer);
        $this->em->flush();
        $this->em->clear();

        $repo = $this->em->getRepository(Customer::class);
        $savedCustomer = $repo->findOneBy(['username' => 'IntegrationUser']);

        $this->assertNotNull($savedCustomer);

        // Test every getter for the fields you set
        $this->assertSame('IntegrationUser', $savedCustomer->getUsername());
        $this->assertSame('John', $savedCustomer->getFirstName());
        $this->assertSame('Doe', $savedCustomer->getLastName());
        $this->assertSame('Paris', $savedCustomer->getCity());
        $this->assertSame('75000', $savedCustomer->getPostalCode());
        $this->assertSame('ACME Inc', $savedCustomer->getCompanyName());
        $this->assertSame(['ROLE_USER'], $savedCustomer->getRoles());

        // Type assertions for UUID and CreatedAt
        $this->assertInstanceOf(Uuid::class, $savedCustomer->getUuid());
        $this->assertInstanceOf(DateTimeImmutable::class, $savedCustomer->getCreatedAt());

        // Id is integer and > 0
        $this->assertIsInt($savedCustomer->getId());
        $this->assertGreaterThan(0, $savedCustomer->getId());

        // Password verification
        $this->assertTrue(
            password_verify($plainPassword, $savedCustomer->getPassword()),
            'The hashed password should verify against the original plain password'
        );
    }
}