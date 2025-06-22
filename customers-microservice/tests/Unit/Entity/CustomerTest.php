<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Customer;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

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

        $this->customer->eraseCredentials();

        $this->assertNull($this->customer->getPlainPassword());
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

    public function testCreatedAt(): void{
        $this->customer->setCreatedAtValue();
        $this->assertInstanceOf(DateTimeImmutable::class, $this->customer->getCreatedAt(), 'DateTimeImmutable was expected');

        $this->customer->setCreatedAt(new DateTimeImmutable('2020-02-02 20:20:20'));
        $this->assertEquals(new DateTimeImmutable('2020-02-02 20:20:20'), $this->customer->getCreatedAt());
    }


    public function testUuid(): void{
        $this->assertNull($this->customer->getUuid());
        $this->customer->setUuidValue(); //I don't know what the value will be???
        $this->assertInstanceOf(Uuid::class, $this->customer->getUuid(), '');

        $uuid = Uuid::v4();
        $this->customer->setUuid($uuid);
        $this->assertEquals($uuid, $this->customer->getUuid());
    }

    public function testUsernameAndUserIdentifier(): void{

        $this->assertSame('', $this->customer->getUserIdentifier());

        $this->assertSame(null, $this->customer->getUsername());
        $this->customer->setUsername('MySuperUsername');
        $this->assertSame('MySuperUsername', $this->customer->getUsername());

         $this->assertSame('MySuperUsername', $this->customer->getUserIdentifier());

    }

    
    public function testFirstName(): void{

        $this->assertSame(null, $this->customer->getFirstName());
        $this->customer->setFirstName('MySuperFirstName');
        $this->assertSame('MySuperFirstName', $this->customer->getFirstName());
    }

    public function testLastName(): void{

        $this->assertSame(null, $this->customer->getLastName());
        $this->customer->setLastName('MySuperLastName');
        $this->assertSame('MySuperLastName', $this->customer->getLastName());
    }

    public function testPostalCode(): void{

        $this->assertSame(null, $this->customer->getPostalCode());
        $this->customer->setPostalCode('MySuperPostalCode');
        $this->assertSame('MySuperPostalCode', $this->customer->getPostalCode());
    }

    
    public function testCity(): void{

        $this->assertSame(null, $this->customer->getCity());
        $this->customer->setCity('MySuperCity');
        $this->assertSame('MySuperCity', $this->customer->getCity());
    }

    
    public function testCompanyName(): void{

        $this->assertSame(null, $this->customer->getCompanyName());
        $this->customer->setCompanyName('MySuperCompanyName');
        $this->assertSame('MySuperCompanyName', $this->customer->getCompanyName());
    }

    public function testRoles(): void{
        $this->assertContains('ROLE_USER', $this->customer->getRoles());
        $this->customer->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        $this->assertSame(['ROLE_USER', 'ROLE_ADMIN'], $this->customer->getRoles());
    }

    public function testId(): void{
        $this->assertNull($this->customer->getId());
    }

}