<?php

namespace App\Security;

use App\Entity\Project;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ProjectVoter extends Voter
{
    public const VIEW = 'view';
    public const EDIT = 'edit';

    public const CREATE_TASK = 'create_task';

    public function __construct(
        private AccessDecisionManagerInterface $accessDecisionManager,
    ) {
    }

    /**
     * Vérifie si l'utilisateur du Voter est légitime.
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::VIEW, self::EDIT, self::CREATE_TASK])) {
            return false;
        }

        if (!$subject instanceof Project) {
            return false;
        }

        return true;
    }

    /**
     * Vérifie les droits d'accès.
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            $vote?->addReason("L'utilisateur n'est pas connecté.");

            return false;
        }

        if ($this->accessDecisionManager->decide($token, ['ROLE_ADMIN'])) {
            return true;
        }

        /** @var Project $project */
        $project = $subject;

        return match ($attribute) {
            self::VIEW => $this->canView($project, $user),
            self::EDIT => $this->canEdit($project, $user, $vote),
            self::CREATE_TASK => $this->canCreateTask($project, $user, $vote),
            default => throw new \LogicException('This code should not be reached!'),
        };
    }

    /**
     * Vérifie si l'utilisateur peut visualiser le projet.
     */
    public function canView(Project $project, User $user): bool
    {
        if ($this->canEdit($project, $user, null)) {
            return true;
        }

        return $project->isContributor($user);
    }

    /**
     * Vérifie si l'utilisateur peut mettre à jour le projet.
     */
    public function canEdit(Project $project, User $user, ?Vote $vote): bool
    {
        if ($user === $project->getOwner()) {
            return true;
        }

        $vote?->addReason(sprintf(
            "L'utilisateur connecté (email: %s) n'est pas l'auteur de ce projet (projet: %d).",
            $user->getEmail(), $project->getName()
        ));

        return false;
    }

    /**
     * Vérifie si l'utilisateur peut mettre à jour les tâches du projet.
     */
    public function canCreateTask(Project $project, User $user, ?Vote $vote): bool
    {
        if ($this->canEdit($project, $user, $vote)) {
            return true;
        }

        if ($project->isContributor($user)) {
            return true;
        }

        $vote?->addReason(sprintf(
            "L'utilisateur connecté (email: %s) n'est pas contributeur de ce projet (projet: %d).",
            $user->getEmail(), $project->getName()
        ));

        return false;
    }
}
