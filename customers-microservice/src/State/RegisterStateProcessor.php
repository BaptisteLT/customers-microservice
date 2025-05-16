<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Customer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterStateProcessor implements ProcessorInterface
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

        //We check if the username already exist
        $existingUser = $this->entityManager->getRepository(Customer::class)
        ->findOneBy(['username' => $data->getUsername()]);

        if ($existingUser) {
            // If found, throw 409 Conflict error
            throw new ConflictHttpException(sprintf('Username "%s" existe déjà.', $data->getUsername()));
        }


        //We check if the paswword is set and if it is, we hash it to the DB
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
