<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Exception\InvalidArgumentException;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * Rôle administrateur.
     */
    const ROLE_ADMIN = 'ROLE_ADMIN';

    /**
     * Rôle utilisateur.
     */
    const ROLE_USER = 'ROLE_USER';

    /**
     * Liste des rôles.
     */
    const ROLES = [self::ROLE_ADMIN, self::ROLE_USER];

    /**
     * Identifiant unique de l'utilisateur.
     *
     * @var int|null
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Email de l'utilisateur.
     *
     * @var string|null
     */
    #[ORM\Column(length: 180)]
    #[Assert\Email(
        message: "{{ value }} n'est pas une adresse email valide.",
    )]
    private ?string $email = null;

    /**
     * Rôles associés à l'utilisateur.
     *
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * Mot de passe de l'utilisateur.
     *
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    /**
     * Prénom de l'utilisateur.
     *
     * @var string|null
     */
    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    /**
     * Nom de l'utilisateur.
     *
     * @var string|null
     */
    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    /**
     * Liste des projets dont l'utilisateur est propriétaire.
     *
     * @var Collection<int, Project>
     */
    #[ORM\OneToMany(targetEntity: Project::class, mappedBy: 'user')]
    private Collection $projectOwnerships;

    /**
     * @var Collection<int, Project>
     */
    #[ORM\ManyToMany(targetEntity: Project::class, mappedBy: 'contributors')]
    private Collection $projectContributions;

    public function __construct()
    {
        $this->projectOwnerships = new ArrayCollection();
        $this->projectContributions = new ArrayCollection();
    }

    /**
     * Retourne l'identifiant unique de l'utilisateur.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
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
     * Initialise et retourne l'email de l'utilisateur.
     *
     * @param string $email
     *
     * @return $this
     */
    public function setEmail(string $email): static
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->email = $email;
        } else {
            throw new InvalidArgumentException(sprintf("%s n'est pas une adresse email valide.", $email));
        }

        return $this;
    }

    /**
     * Retourne le libellé de l'utilisateur.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * Retourne les rôles associés à l'utilisateur.
     *
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * Retourne les libellés des rôles associés à l'utilisateur.
     *
     * @return array
     */
    public function getRolesLibelled(): array
    {
        $roles = $this->getRoles();

        return array_map(function($role) {
            return match ($role) {
                self::ROLE_ADMIN => 'Administrateur',
                self::ROLE_USER => 'Utilisateur',
            };
        }, $roles);
    }

    /**
     * Initialise les rôles associés à l'utilisateur et retourne l'utilisateur.
     *
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Retourne le mot de passe de l'utilisateur.
     *
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Initialise le mot de passe de l'utilisateur et retourne l'utilisateur.
     *
     * @param string $password
     *
     * @return $this
     */
    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
    }

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
     * Initialise le prénom de l'utilisateur et retourne l'utilisateur.
     *
     * @param string $firstname
     *
     * @return $this
     */
    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
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
     * Initialise le nom de l'utilisateur et retourne l'utilisateur.
     *
     * @param string $lastname
     *
     * @return $this
     */
    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Retourne le nom complet de l'utilisateur.
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return "{$this->firstname} {$this->lastname}";
    }

    /**
     * Retourne la liste des projets dont l'utilisateur est propriétaire.
     *
     * @return Collection<int, Project>
     */
    public function getProjectOwnerships(): Collection
    {
        return $this->projectOwnerships;
    }

    /**
     * Ajoute un projet propriétaire à l'utilisateur et retourne l'utilisateur.
     *
     * @param Project $project
     *
     * @return $this
     */
    public function addProjectOwnership(Project $project): static
    {
        if (!$this->projectOwnerships->contains($project)) {
            $this->projectOwnerships->add($project);
            $project->setOwner($this);
        }

        return $this;
    }

    /**
     * Supprime un projet propriétaire de l'utilisateur et retourne l'utilisateur.
     *
     * @param Project $project
     *
     * @return $this
     */
    public function removeProjectOwnership(Project $project): static
    {
        if ($this->projectOwnerships->removeElement($project)) {
            // set the owning side to null (unless already changed)
            if ($project->getOwner() === $this) {
                $project->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * Retourne la liste des projets dont l'utilisateur est contributeur.
     *
     * @return Collection<int, Project>
     */
    public function getProjectContributions(): Collection
    {
        return $this->projectContributions;
    }

    /**
     * Ajoute un projet de contribution à l'utilisateur et retourne l'utilisateur.
     *
     * @param Project $projectContribution
     *
     * @return $this
     */
    public function addProjectContribution(Project $projectContribution): static
    {
        if (!$this->projectContributions->contains($projectContribution)) {
            $this->projectContributions->add($projectContribution);
            $projectContribution->addContributor($this);
        }

        return $this;
    }

    /**
     * Supprime un projet de contribution de l'utilisateur et retourne l'utilisateur.
     *
     * @param Project $projectContribution
     *
     * @return $this
     */
    public function removeProjectContribution(Project $projectContribution): static
    {
        if ($this->projectContributions->removeElement($projectContribution)) {
            $projectContribution->removeContributor($this);
        }

        return $this;
    }
}
