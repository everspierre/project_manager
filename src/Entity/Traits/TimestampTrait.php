<?php

namespace App\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait TimestampTrait
{
    /**
     * Date de création.
     */
    #[ORM\Column]
    public ?\DateTime $createdAt = null;

    /**
     * Date de modification.
     */
    #[ORM\Column]
    public ?\DateTime $updatedAt = null;

    /**
     * Retourne la date de création.
     */
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt ?? new \DateTime();
    }

    /**
     * Initialise la date de création.
     */
    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Retourne la date de modification.
     */
    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt ?? new \DateTime();
    }

    /**
     * Initialise la date de modification.
     */
    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updateTimestamps(): void
    {
        $now = new \DateTime();
        $this->setUpdatedAt($now);
        if ($this->getId() === null) {
            $this->setCreatedAt($now);
        }
    }
}
