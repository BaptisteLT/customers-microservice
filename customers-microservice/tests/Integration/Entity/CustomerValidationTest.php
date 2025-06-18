<?php

namespace App\Tests\Integration\Entity;

use App\Entity\Customer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CustomerValidationTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = static::getContainer()->get('doctrine')->getManager();
        $this->entityManager->beginTransaction();
    }

    public function testValidCustomer(): void
    {
        self::bootKernel();
        $validator = static::getContainer()->get(ValidatorInterface::class);

        $customer = new Customer();
        $customer->setUsername('valid_user');
        $customer->setFirstName('Alice');
        $customer->setLastName('Durand');
        $customer->setCreatedAt(new \DateTimeImmutable());

        $violations = $validator->validate($customer);
        $this->assertCount(0, $violations, "Customer valide ne devrait générer aucune violation");
    }

    public function testMissingUsername(): void
    {
        self::bootKernel();
        $validator = static::getContainer()->get(ValidatorInterface::class);

        $customer = new Customer();
        $customer->setFirstName('Alice');
        $customer->setLastName('Durand');
        $customer->setCreatedAt(new \DateTimeImmutable());

        $violations = $validator->validate($customer);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function testInvalidEmailFormat(): void
    {
        self::bootKernel();
        $validator = static::getContainer()->get(ValidatorInterface::class);

        $customer = new Customer();
        $customer->setUsername('bad_email_user');
        $customer->setFirstName('Jean');
        $customer->setLastName('Dubois');
        $customer->setEmail('not-an-email');
        $customer->setCreatedAt(new \DateTimeImmutable());

        $violations = $validator->validate($customer);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function testCreatedAtIsValid(): void
    {
        $customer = new Customer();
        $now = new \DateTimeImmutable();
        $customer->setCreatedAt($now);

        $this->assertInstanceOf(\DateTimeImmutable::class, $customer->getCreatedAt());
        $this->assertEquals($now, $customer->getCreatedAt());
    }

    public function testUsernameUniqueness()
    {
        // Créer un premier utilisateur avec un username spécifique
        $customer1 = new Customer();
        $customer1->setUsername('unique_user');
        $customer1->setFirstName('John');
        $customer1->setLastName('Doe');
        $customer1->setCreatedAt(new \DateTimeImmutable());

        // Persister le premier utilisateur dans la base de données
        $this->entityManager->persist($customer1);
        $this->entityManager->flush();

        // Vérifier que le premier utilisateur a été correctement inséré
        $this->assertNotNull($customer1->getId());

        // Créer un deuxième utilisateur avec le même username
        $customer2 = new Customer();
        $customer2->setUsername('unique_user');
        $customer2->setFirstName('Jane');
        $customer2->setLastName('Smith');
        $customer2->setCreatedAt(new \DateTimeImmutable());

        // Persister le deuxième utilisateur dans la base de données
        $this->entityManager->persist($customer2);

        // Vérifier qu'une exception est levée lors de la tentative de flush
        $this->expectException(\Doctrine\DBAL\Exception\UniqueConstraintViolationException::class);
        $this->entityManager->flush();
    }

    protected function tearDown(): void
    {
        $this->entityManager->rollback(); // Pas de flush final, donc rien de vraiment écrit
        $this->entityManager->close();
        $this->entityManager = null;
        parent::tearDown();
    }
}
