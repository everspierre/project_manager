<?php

namespace App\EventListener\BlameableListener;

use App\Entity\Traits\BlameableEntity;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[AsDoctrineListener(event: Events::prePersist, priority: 500, connection: 'default')]
class PrePersistListener
{
    public function __construct(private readonly TokenStorageInterface $tokenStorage)
    {
    }

    /**
     * Initialise le créateur de l'objet.
     */
    public function prePersist(LifecycleEventArgs $event): void
    {
        $entity = $event->getObject();

        if (in_array(BlameableEntity::class, array_keys((new \ReflectionClass($entity))->getTraits()))) {
            if (null === $entity->getId() && method_exists($entity, 'setCreatedBy')) {
                $entity->setCreatedBy($this->getUser());
            }
            if (method_exists($entity, 'setUpdatedBy')) {
                $entity->setUpdatedBy($this->getUser());
            }
        }
    }

    /**
     * Retourne l'utilisateur connecté.
     *
     * @return UserInterface|null
     */
    public function getUser(): ?UserInterface
    {
        if (!$this->tokenStorage) {
            throw new \LogicException('The SecurityBundle is not registered in your application.');
        }

        if (null === $token = $this->tokenStorage->getToken()) {
            return null;
        }

        if (!is_object($user = $token->getUser())) {
            return null;
        }

        return $user;
    }
}
