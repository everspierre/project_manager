<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
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
     * @inheritDoc
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::VIEW, self::EDIT])) {
            return false;
        }

        if (!$subject instanceof User) {
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
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
     * Vérifie si l'utilisateur connecté peut visualiser l'utilisateur.
     *
     * @param User $user
     * @param User $connectedUser
     *
     * @return bool
     */
    public function canView(User $user, User $connectedUser): bool
    {
        if ($this->canEdit($user, $connectedUser, null)) {
            return true;
        }

        return false;
    }

    /**
     * Vérifie si l'utilisateur connecté peut mettre à jour l'utilisateur.
     *
     * @param User $user
     * @param User $connectedUser
     * @param Vote|null $vote
     *
     * @return bool
     */
    public function canEdit(User $user, User $connectedUser, ?Vote $vote): bool
    {
        if ($user->getId() === $connectedUser->getId()) {
            return true;
        }

        $vote?->addReason(sprintf(
            "L'utilisateur connecté (email: %s) n'est pas autorisé.",
            $connectedUser->getEmail()
        ));

        return false;
    }
}
