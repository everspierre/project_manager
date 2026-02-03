<?php

namespace App\Entity;

use App\Entity\Traits\BlameableEntity;
use App\Entity\Traits\TimestampableEntity;
use App\Repository\TaskRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Exception\InvalidArgumentException;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Task
{
    use TimestampableEntity;

    use BlameableEntity;

    /**
     * Etat "en attente".
     */
    public const STATE_WAITING = 'en_attente';

    /**
     * Etat "en cours".
     */
    public const STATE_RUNNING = 'en_cours';

    /**
     * Etat "annulée".
     */
    public const STATE_CANCELLED = 'annulée';

    /**
     * Etat "terminée".
     */
    public const STATE_COMPLETED = 'terminée';

    /**
     * Etat "supprimée".
     */
    public const STATE_REMOVED = 'supprimée';

    /**
     * Liste des états.
     */
    public const STATES = [
        self::STATE_WAITING,
        self::STATE_RUNNING,
        self::STATE_CANCELLED,
        self::STATE_COMPLETED,
        self::STATE_REMOVED,
    ];

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
     * Etat de la tâche.
     */
    #[ORM\Column(length: 255)]
    private ?string $state = self::STATE_WAITING;

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

    /**
     * Retourne l'état de la tâche.
     */
    public function getState(): ?string
    {
        return $this->state;
    }

    /**
     * Retourne le libellé de l'état de la tâche.
     */
    public function getStateLibelled(): ?string
    {
        return match ($this->state) {
            self::STATE_WAITING => 'En attente',
            self::STATE_RUNNING => 'En cours',
            self::STATE_COMPLETED => 'Terminée',
            self::STATE_CANCELLED => 'Annulée',
            self::STATE_REMOVED => 'Supprimée',
            default => '?',
        };
    }

    /**
     * Retourne la classe Css de l'état de la tâche.
     */
    public function getStateTableClass(): ?string
    {
        return match ($this->state) {
            self::STATE_WAITING => 'table-secondary',
            self::STATE_RUNNING => 'table-warning',
            self::STATE_COMPLETED => 'table-success',
            self::STATE_CANCELLED => 'table-light',
            self::STATE_REMOVED => 'table-danger',
            default => '',
        };
    }

    /**
     * Initialise et retourne l'état de la tâche.
     *
     * @return $this
     */
    public function setState(string $state): static
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Vérifie si la tâche est supprimée.
     */
    public function isRemoved(): bool
    {
        return self::STATE_REMOVED === $this->state;
    }
}
