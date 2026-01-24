<?php

namespace App\Security;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TaskVoter extends Voter
{
    const VIEW = 'view';
    const EDIT = 'edit';

    /**
     * @param AccessDecisionManagerInterface $accessDecisionManager
     */
    public function __construct(
        private AccessDecisionManagerInterface $accessDecisionManager,
    ) {
    }

    /**
     * Vérifie si l'utilisateur du Voter est légitime.
     *
     * @param string $attribute
     * @param mixed $subject
     *
     * @return bool
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::VIEW, self::EDIT])) {
            return false;
        }

        if (!$subject instanceof Task) {
            return false;
        }

        return true;
    }

    /**
     * Vérifie les droits d'accès.
     *
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     * @param Vote|null $vote
     *
     * @return bool
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

        return match($attribute) {
            self::VIEW => $this->canView($subject, $user),
            self::EDIT => $this->canEdit($subject, $user, $vote),
            default => throw new \LogicException('This code should not be reached!')
        };
    }

    /**
     * Vérifie si l'utilisateur peut visualiser la tâche.
     *
     * @param Task|null $task
     * @param User $user
     *
     * @return bool
     */
    public function canView(?Task $task, User $user): bool
    {
        if ($this->canEdit($task, $user, null)) {
            return true;
        }

        return false;
    }

    /**
     * Vérifie si l'utilisateur peut mettre à jour la tâche.
     *
     * @param Task|null $task
     * @param User $user
     * @param Vote|null $vote
     *
     * @return bool
     */
    public function canEdit(?Task $task, User $user, ?Vote $vote): bool
    {
        if ($task->getProject()->isOwner($user)) {
            return true;
        }

        if ($task->getProject()->isContributor($user)) {
            return true;
        }

        $vote?->addReason(sprintf(
            "L'utilisateur connecté (email: %s) n'est pas contributeur de ce projet (projet: %d).",
            $user->getEmail(), $task?->getProject()->getName()
        ));

        return false;
    }
}
