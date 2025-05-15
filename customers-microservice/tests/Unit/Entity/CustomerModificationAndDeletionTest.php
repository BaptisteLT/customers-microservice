<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Customer;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class CustomerModificationAndDeletionTest extends TestCase
{
    public function testUpdateCustomer()
    {
        // Créer un mock de l'EntityManager
        $entityManager = $this->createMock(EntityManagerInterface::class);

        // Créer un nouveau Customer
        $customer = new Customer();
        $customer->setUsername('john_doe');
        $customer->setFirstName('John');
        $customer->setLastName('Doe');
        $customer->setCreatedAt(new \DateTimeImmutable());

        // Simuler la persistance et le flush
        $entityManager->expects($this->once())
            ->method('persist')
            ->with($customer);

        $entityManager->expects($this->once())
            ->method('flush');

        // Mettre à jour les informations du Customer
        $customer->setFirstName('Jane');
        $customer->setLastName('Smith');

        // Persister les modifications
        $entityManager->persist($customer);
        $entityManager->flush();

        // Vérifier que les modifications ont été correctement appliquées
        $this->assertEquals('Jane', $customer->getFirstName());
        $this->assertEquals('Smith', $customer->getLastName());
    }

    public function testDeleteCustomer()
    {
        // Créer un mock de l'EntityManager
        $entityManager = $this->createMock(EntityManagerInterface::class);

        // Créer un nouveau Customer
        $customer = new Customer();
        $customer->setUsername('john_doe');
        $customer->setFirstName('John');
        $customer->setLastName('Doe');
        $customer->setCreatedAt(new \DateTimeImmutable());

        // Simuler la suppression et le flush
        $entityManager->expects($this->once())
            ->method('remove')
            ->with($customer);

        $entityManager->expects($this->once())
            ->method('flush');

        // Supprimer le Customer
        $entityManager->remove($customer);
        $entityManager->flush();
    }
}
