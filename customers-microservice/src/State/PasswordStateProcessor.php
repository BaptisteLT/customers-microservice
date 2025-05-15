<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Customer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PasswordStateProcessor implements ProcessorInterface
{
    public function __construct(private EntityManagerInterface $entityManager, private UserPasswordHasherInterface $passwordHasher)
    {

    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): object
    {
        if (!$data instanceof Customer) {
            // We only support Customer entity here, so just return other data untouched
            return $data;
        }

        if ($data->getPlainPassword()) {
            $hashedPassword = $this->passwordHasher->hashPassword($data, $data->getPlainPassword());
            $data->setPassword($hashedPassword);
            $data->eraseCredentials();
        }

        $this->entityManager->persist($data);
        $this->entityManager->flush();

        return $data;
    }
}
