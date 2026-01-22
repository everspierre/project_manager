<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{
    /**
     * Identifiant unique du projet.
     *
     * @var int|null
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Nom du projet.
     *
     * @var string|null
     */
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * Description du projet.
     *
     * @var string|null
     */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    /**
     * Liste des tâches associées.
     *
     * @var Collection<int, Task>
     */
    #[ORM\OneToMany(targetEntity: Task::class, mappedBy: 'project', orphanRemoval: true)]
    private Collection $tasks;

    /**
     * Utilisateur propriétaire du projet.
     *
     * @var User|null
     */
    #[ORM\ManyToOne(inversedBy: 'projectOwnerships')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    /**
     * Utilisateurs contributeurs du projet.
     *
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'projectContributions')]
    private Collection $contributors;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
        $this->contributors = new ArrayCollection();
    }

    /**
     * Retourne l'identifiant unique du projet.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

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
     * Initialise et retourne le nom du projet.
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
     * Retourne la description du projet.
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Initialise et retourne la description du projet.
     *
     * @param string|null $description
     *
     * @return $this
     */
    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Retourne la liste des tâches associées au projet.
     *
     * @return Collection<int, Task>
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    /**
     * Associe la tâche au projet et retourne le projet.
     *
     * @param Task $task
     *
     * @return $this
     */
    public function addTask(Task $task): static
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks->add($task);
            $task->setProject($this);
        }

        return $this;
    }

    /**
     * Supprime l'association de la tâche au projet et retourne le projet.
     *
     * @param Task $task
     *
     * @return $this
     */
    public function removeTask(Task $task): static
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getProject() === $this) {
                $task->setProject(null);
            }
        }

        return $this;
    }

    /**
     * Retourne le propriétaire du projet.
     *
     * @return User|null
     */
    public function getOwner(): ?User
    {
        return $this->owner;
    }

    /**
     * Initialise le propriétaire du projet et retourne le projet.
     *
     * @param User|null $owner
     *
     * @return $this
     */
    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Vérifie si l'utilisateur est le propriétaire.
     *
     * @param User $user
     *
     * @return bool
     */
    public function isOwner(User $user): bool
    {
        return $this->owner->getId() === $user->getId();
    }

    /**
     * Retourne les contributeurs du projet.
     *
     * @return Collection<int, User>
     */
    public function getContributors(): Collection
    {
        return $this->contributors;
    }

    /**
     * Ajoute un contributeur au projet et retourne le projet.
     *
     * @param User $contributor
     *
     * @return $this
     */
    public function addContributor(User $contributor): static
    {
        if (!$this->contributors->contains($contributor)) {
            $this->contributors->add($contributor);
        }

        return $this;
    }

    /**
     * Supprime un contributeur du projet et retourne le projet.
     *
     * @param User $contributor
     *
     * @return $this
     */
    public function removeContributor(User $contributor): static
    {
        $this->contributors->removeElement($contributor);

        return $this;
    }

    /**
     * Vérifie si un utilisateur est contributeur du projet.
     *
     * @param User $user
     *
     * @return bool
     */
    public function isContributor(User $user): bool
    {
        return $this->contributors->contains($user);
    }
}
