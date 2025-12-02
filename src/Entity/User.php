<?php

declare(strict_types=1);

namespace ADS\UCCIA\Entity;

use ADS\UCCIA\Entity\Enums\OriginType;
use ADS\UCCIA\Entity\Traits\WithUuid;
use ADS\UCCIA\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'app_users')]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use WithUuid;

    #[Assert\NotBlank]
    #[Assert\Email]
    #[Assert\Length(['max' => 100])]
    #[ORM\Column(length: 180)]
    private string $email;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column(type: Types::JSON)]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[Assert\NotBlank]
    #[Assert\Length(['min' => 8, 'max' => 4096])]
    #[ORM\Column]
    private string $password;

    #[Assert\NotBlank]
    #[ORM\Column(length: 50, enumType: OriginType::class, options: ['default' => OriginType::CCIA_NGAZIDJA])]
    private OriginType $origin = OriginType::CCIA_NGAZIDJA;

    public function __construct()
    {
        $this->id = Uuid::v7();
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getOrigin(): OriginType
    {
        return $this->origin;
    }

    public function setOrigin(OriginType $origin): static
    {
        $this->origin = $origin;

        return $this;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
    }
}
