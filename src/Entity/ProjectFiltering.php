<?php

namespace App\Entity;

class ProjectFiltering
{
    /**
     * Nom du projet.
     *
     * @var string|null
     */
    private ?string $name = null;

    /**
     * Retourne le nom du projet.
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Initialise le nom du projet.
     *
     * @param string|null $name
     *
     * @return void
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }
}
