<?php

namespace App\Tests\Integration\Service;

use App\Entity\Customer;
use App\Service\CustomerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CustomerServiceTest extends KernelTestCase
{
    private $entityManager;
    private $customerService;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->customerService = new CustomerService($this->entityManager);
    }

    public function testCreateCustomer()
    {
        $customer = new Customer();
        $customer->setFirstName('John');
        $customer->setLastName('Doe');
        $customer->setEmail('john.doe@example.com');

        $this->customerService->createCustomer($customer);

        $this->assertNotNull($customer->getId());
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }
}
