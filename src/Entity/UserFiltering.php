<?php

namespace App\Entity;

class UserFiltering
{
    /**
     * Prénom de l'utilisateur.
     *
     * @var string|null
     */
    private ?string $firstname = null;

    /**
     * Nom de l'utilisateur.
     *
     * @var string|null
     */
    private ?string $lastname = null;

    /**
     * Email de l'utilisateur.
     *
     * @var string|null
     */
    private ?string $email = null;

    /**
     * Rôle de l'utilisateur.
     *
     * @var string|null
     */
    private ?string $role = null;

    /**
     * Retourne le prénom de l'utilisateur.
     *
     * @return string|null
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * Initialise le prénom de l'utilisateur.
     *
     * @param string|null $firstname
     *
     * @return void
     */
    public function setFirstname(?string $firstname): void
    {
        $this->firstname = $firstname;
    }

    /**
     * Retourne le nom de l'utilisateur.
     *
     * @return string|null
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * Initialise le prénom de l'utilisateur.
     *
     * @param string|null $lastname
     *
     * @return void
     */
    public function setLastname(?string $lastname): void
    {
        $this->lastname = $lastname;
    }

    /**
     * Retourne l'email de l'utilisateur.
     *
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Initialise l'email de l'utilisateur.
     *
     * @param string|null $email
     *
     * @return void
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    /**
     * Retourne le rôle de l'utilisateur.
     *
     * @return string|null
     */
    public function getRole(): ?string
    {
        return $this->role;
    }

    /**
     * Initialise le rôle de l'utilisateur.
     *
     * @param string|null $role
     *
     * @return void
     */
    public function setRole(?string $role): void
    {
        $this->role = $role;
    }
}
