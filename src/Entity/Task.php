<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Exception\InvalidArgumentException;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
    /**
     * Identifiant unique de la tâche.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Nom de la tâche.
     */
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * Description de la tâche.
     */
    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    /**
     * Date de début de la tâche.
     */
    #[ORM\Column]
    private ?\DateTime $startingDate = null;

    /**
     * Date de fin de la tâche.
     */
    #[ORM\Column(nullable: true)]
    #[Assert\GreaterThanOrEqual(propertyPath: 'startingDate', message: 'La date de fin doit être supérieure ou égale à la date de début')]
    private ?\DateTime $endingDate = null;

    /**
     * Projet associé à la tâche.
     */
    #[ORM\ManyToOne(inversedBy: 'tasks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Project $project = null;

    /**
     * Retourne l'identifiant unique de la tâche.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Retourne le nom de la tâche.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Initialise et retourne le nom de la tâche.
     *
     * @return $this
     */
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Retourne la description de la tâche.
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Initialise et retourne la description de la tâche.
     *
     * @return $this
     */
    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Retourne la date de début de la tâche.
     */
    public function getStartingDate(): ?\DateTime
    {
        return $this->startingDate;
    }

    /**
     * Initialise et retourne la date de début de la tâche.
     *
     * @return $this
     */
    public function setStartingDate(\DateTime $startingDate): static
    {
        $this->startingDate = $startingDate;

        return $this;
    }

    /**
     * Retourne la date de fin de la tâche.
     */
    public function getEndingDate(): ?\DateTime
    {
        return $this->endingDate;
    }

    /**
     * Initialise et retourne la date de fin de la tâche.
     *
     * @return $this
     */
    public function setEndingDate(?\DateTime $endingDate): static
    {
        if (is_null($this->startingDate) && !is_null($endingDate)) {
            throw new InvalidArgumentException('Aucune date de début renseignée.');
        }

        if (!is_null($endingDate) && !is_null($this->startingDate) && $this->startingDate > $endingDate) {
            throw new InvalidArgumentException(sprintf('La date de fin doit être supérieure ou égale à la date de début (%s).', $this->startingDate->format('d-m-Y')));
        }

        $this->endingDate = $endingDate;

        return $this;
    }

    /**
     * Retourne le projet associé à la tâche.
     */
    public function getProject(): ?Project
    {
        return $this->project;
    }

    /**
     * Associe le projet à la tâche et retourne la tâche.
     *
     * @return $this
     */
    public function setProject(?Project $project): static
    {
        $this->project = $project;

        return $this;
    }
}
