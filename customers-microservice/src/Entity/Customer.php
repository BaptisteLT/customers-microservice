<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\CustomerRepository;
use App\State\PasswordStateProcessor;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\Ignore;

#Lors de la MAJ d'un customer ou de la création on hash le MDP
#[Post(processor: PasswordStateProcessor::class)]
#[Put(processor: PasswordStateProcessor::class)]
#[Patch(processor: PasswordStateProcessor::class)]

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: CustomerRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['customer:read']],
    denormalizationContext: ['groups' => ['customer:write']],
)]
class Customer implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[Groups(["customer:read", "customer:write"])]
    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[Groups(["customer:read", "customer:write"])]
    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[Groups(["customer:read", "customer:write"])]
    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[Groups(["customer:read", "customer:write"])]
    #[ORM\Column(length: 255)]
    private ?string $postalCode = null;

    #[Groups(["customer:read", "customer:write"])]
    #[ORM\Column(length: 255)]
    private ?string $city = null;

    #[Groups(["customer:read", "customer:write"])]
    #[ORM\Column(length: 255)]
    private ?string $companyName = null;

    #[Ignore]
    #[ORM\Column(length: 255)]
    private ?string $password = null;
    
    #[Groups(["customer:write"])]
    private ?string $plainPassword = null;

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): static
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        if ($this->createdAt === null) {
            $this->createdAt = new \DateTimeImmutable();
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): static
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(string $companyName): static
    {
        $this->companyName = $companyName;

        return $this;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function getUserIdentifier(): string
    {
        return $this->username ?? '';
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

}
