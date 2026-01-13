<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
    /**
     * Identifiant unique de la tâche.
     *
     * @var int|null
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Nom de la tâche.
     *
     * @var string|null
     */
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * Description de la tâche.
     *
     * @var string|null
     */
    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    /**
     * Date de début de la tâche.
     *
     * @var \DateTime|null
     */
    #[ORM\Column]
    private ?\DateTime $startingDate = null;

    /**
     * Date de fin de la tâche.
     *
     * @var \DateTime|null
     */
    #[ORM\Column(nullable: true)]
    #[Assert\GreaterThanOrEqual(propertyPath: 'startingDate', message: 'La date de fin doit être supérieure ou égale à la date de début')]
    private ?\DateTime $endingDate = null;

    /**
     * Projet associé à la tâche.
     *
     * @var Project|null
     */
    #[ORM\ManyToOne(inversedBy: 'tasks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Project $project = null;

    /**
     * Retourne l'identifiant unique de la tâche.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Retourne le nom de la tâche.
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Initialise et retourne le nom de la tâche.
     *
     * @param string $name
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
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Initialise et retourne la description de la tâche.
     *
     * @param string $description
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
     *
     * @return \DateTime|null
     */
    public function getStartingDate(): ?\DateTime
    {
        return $this->startingDate;
    }

    /**
     * Initialise et retourne la date de début de la tâche.
     *
     * @param \DateTime $startingDate
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
     *
     * @return \DateTime|null
     */
    public function getEndingDate(): ?\DateTime
    {
        return $this->endingDate;
    }

    /**
     * Initialise et retourne la date de fin de la tâche.
     *
     * @param \DateTime|null $endingDate
     *
     * @return $this
     */
    public function setEndingDate(?\DateTime $endingDate): static
    {
        $this->endingDate = $endingDate;

        return $this;
    }

    /**
     * Retourne le projet associé à la tâche.
     *
     * @return Project|null
     */
    public function getProject(): ?Project
    {
        return $this->project;
    }

    /**
     * Associe le projet à la tâche et retourne la tâche.
     *
     * @param Project|null $project
     *
     * @return $this
     */
    public function setProject(?Project $project): static
    {
        $this->project = $project;

        return $this;
    }
}
