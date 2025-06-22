<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\Customer;
use PHPUnit\Framework\Attributes\UsesClass;


class AppFixtures extends Fixture
{

    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {

    }


    public function load(ObjectManager $manager): void
    {
        $customer = new Customer();
        $customer->setUsername('admin@example.com');
        $customer->setFirstName('first');
        $customer->setLastName('last');
        $customer->setPostalCode('10');
        $customer->setCity('Chartres');
        $customer->setCompanyName('cci');
        $customer->setRoles(['ROLE_ADMIN']);
        $customer->setPassword($this->passwordHasher->hashPassword($customer, '$3cr3t'));
        $manager->persist($customer);
        $manager->flush();
    }
}
