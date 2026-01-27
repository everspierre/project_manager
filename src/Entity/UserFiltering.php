<?php

namespace App\Entity;

class UserFiltering
{
    /**
     * Prénom de l'utilisateur.
     */
    private ?string $firstname = null;

    /**
     * Nom de l'utilisateur.
     */
    private ?string $lastname = null;

    /**
     * Email de l'utilisateur.
     */
    private ?string $email = null;

    /**
     * Rôle de l'utilisateur.
     */
    private ?string $role = null;

    /**
     * Retourne le prénom de l'utilisateur.
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * Initialise le prénom de l'utilisateur.
     */
    public function setFirstname(?string $firstname): void
    {
        $this->firstname = $firstname;
    }

    /**
     * Retourne le nom de l'utilisateur.
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * Initialise le prénom de l'utilisateur.
     */
    public function setLastname(?string $lastname): void
    {
        $this->lastname = $lastname;
    }

    /**
     * Retourne l'email de l'utilisateur.
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Initialise l'email de l'utilisateur.
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    /**
     * Retourne le rôle de l'utilisateur.
     */
    public function getRole(): ?string
    {
        return $this->role;
    }

    /**
     * Initialise le rôle de l'utilisateur.
     */
    public function setRole(?string $role): void
    {
        $this->role = $role;
    }
}
