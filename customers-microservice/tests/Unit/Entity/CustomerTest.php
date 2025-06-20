<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Customer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CustomerTest extends KernelTestCase
{

    private Customer $customer;

    private ContainerInterface $container;

    protected function setUp(): void
    {
        $this->customer = new Customer();
        self::bootKernel();
        $this->container = static::getContainer();

    }


    public function testSetAndGetPassword()
    {
        $plainPassword = 'mySuperPassword7&';

        $passwordHasher = $this->container->get(UserPasswordHasherInterface::class);
        $this->customer->setPlainPassword($plainPassword);

        $this->assertSame($this->customer->getPlainPassword(), $plainPassword);

        $hashed = $passwordHasher->hashPassword($this->customer, $this->customer->getPlainPassword());
        $this->customer->setPassword($hashed);

        $this->assertTrue(password_verify($plainPassword, $this->customer->getPassword()));
    }

    public function testValidPlainPassword()
    {
        $validator = $this->container->get('validator');
        $customer = new Customer();
        $customer->setPlainPassword('Valid1!password');

        $violations = $validator->validateProperty($customer, 'plainPassword');
        $this->assertCount(0, $violations, 'Expected no validation errors for valid password');
    }

    #[DataProvider('invalidPasswordsProvider')]
    public function testInvalidPlainPasswords(?string $password)
    {
        $validator = $this->container->get('validator');
        $customer = new Customer();
        $customer->setPlainPassword($password);

        $violations = $validator->validateProperty($customer, 'plainPassword');
        $this->assertGreaterThan(0, count($violations), 'Expected validation errors for invalid password');
    }

    public static function invalidPasswordsProvider(): array
    {
        return [
            [null],               // blank
            [''],                 // blank
            ['short1!'],          // too short (<8)
            ['nocaps123!'],       // no uppercase
            ['NOLOWER123!'],      // no lowercase
            ['NoSpecial123'],     // no special character
            ['NoDigit!@#'],       // no digit
            ['      '],           // whitespace only
        ];
    }

}