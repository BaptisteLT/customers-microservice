<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Customer;
use PHPUnit\Framework\TestCase;

class CustomerGettersAndSettersTest extends TestCase
{
    public function testValidGettersAndSetters()
    {
        $customer = new Customer();

        // Test pour l'ID - devrait passer car l'ID est null initialement
        $this->assertNull($customer->getId());

        // Test pour createdAt - devrait passer car createdAt est défini
        $createdAt = new \DateTimeImmutable();
        $customer->setCreatedAt($createdAt);
        $this->assertEquals($createdAt, $customer->getCreatedAt());

        // Test pour username - devrait passer car le username est défini
        $customer->setUsername('john_doe');
        $this->assertEquals('john_doe', $customer->getUsername());
    }

    public function testInvalidGettersAndSetters()
    {
        $customer = new Customer();

        // Test pour l'ID - devrait échouer car l'ID est null initialement
        $this->assertNotNull($customer->getId(), "L'ID devrait être null initialement.");

        // Test pour createdAt - devrait échouer car createdAt n'est pas défini
        $this->assertNotNull($customer->getCreatedAt(), "createdAt devrait être null initialement.");
    }

    public function testValidNameSetters()
    {
        $customer = new Customer();

        // Test pour firstName - devrait passer car le firstName est défini
        $customer->setFirstName('John');
        $this->assertEquals('John', $customer->getFirstName());

        // Test pour lastName - devrait passer car le lastName est défini
        $customer->setLastName('Doe');
        $this->assertEquals('Doe', $customer->getLastName());
    }

    public function testInvalidNameSetters()
    {
        $customer = new Customer();

        // Test pour firstName - devrait échouer car le firstName n'est pas défini
        $this->assertEquals('Unknown', $customer->getFirstName(), "Le firstName devrait être null initialement.");

        // Test pour lastName - devrait échouer car le lastName n'est pas défini
        $this->assertEquals('User', $customer->getLastName(), "Le lastName devrait être null initialement.");
    }
}
