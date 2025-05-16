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
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
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
        parent::tearDown();

        // Fermer l'EntityManager pour éviter les fuites de mémoire
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
