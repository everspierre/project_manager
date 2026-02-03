<?php

namespace App\Entity\Traits;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;

trait BlameableEntity
{
    /**
     * Créateur.
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[JoinColumn(name: 'created_by', referencedColumnName: 'id', nullable: true)]
    public ?User $createdBy = null;

    /**
     * Modificateur.
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[JoinColumn(name: 'updated_by', referencedColumnName: 'id', nullable: true)]
    public ?User $updatedBy = null;

    /**
     * Retourne le créateur.
     */
    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    /**
     * Initialise le créateur.
     */
    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Retourne le modificateur.
     */
    public function getUpdatedBy(): ?User
    {
        return $this->updatedBy;
    }

    /**
     * Initialise le modificateur.
     */
    public function setUpdatedBy(?User $updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }
}
