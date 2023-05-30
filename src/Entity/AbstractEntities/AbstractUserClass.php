<?php

namespace App\Entity\AbstractEntities;

use App\Entity\Service;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\Unique;

/**
 * Classe abstraite pour les entités utilisateur.
 */
#[ORM\MappedSuperclass]
abstract class AbstractUserClass implements
    UserInterface,
    PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    protected ?string $email;

    #[ORM\Column]
    protected ?array $roles = [];

    #[ORM\Column]
    protected ?string $password = null;

    /**
     * Récupère l'ID de l'utilisateur.
     *
     * @return int|null L'ID de l'utilisateur.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Récupère l'adresse e-mail de l'utilisateur.
     *
     * @return string|null L'adresse e-mail de l'utilisateur.
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Définit l'adresse e-mail de l'utilisateur.
     *
     * @param string $email L'adresse e-mail de l'utilisateur.
     * @return self
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Renvoie l'identifiant de l'utilisateur.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * Récupère les rôles de l'utilisateur.
     *
     * @return array Les rôles de l'utilisateur.
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // Garantit que chaque utilisateur a au moins le rôle ROLE_USER
        $roles[] = "ROLE_USER";

        return array_unique($roles);
    }

    /**
     * Définit les rôles de l'utilisateur.
     *
     * @param array $roles Les rôles de l'utilisateur.
     * @return self
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Récupère le mot de passe de l'utilisateur.
     *
     * @return string Le mot de passe de l'utilisateur.
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Définit le mot de passe de l'utilisateur.
     *
     * @param string $password Le mot de passe de l'utilisateur.
     * @return self
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Efface les informations d'identification de l'utilisateur.
     *
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // Si vous stockez des données temporaires sensibles sur l'utilisateur, supprimez-les ici
        // $this->plainPassword = null;
    }
}
