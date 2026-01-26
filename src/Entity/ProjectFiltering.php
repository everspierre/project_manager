<?php

namespace App\Entity;

class ProjectFiltering
{
    /**
     * Nom du projet.
     */
    private ?string $name = null;

    /**
     * Retourne le nom du projet.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Initialise le nom du projet.
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }
}
