<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserFiltering;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry, private Security $security)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * Retourne les utilisateurs.
     */
    public function findAllPaginated(?UserFiltering $userFiltering): Query
    {
        $query = $this->createQueryBuilder('u')
            ->orderBy('u.id', 'DESC')
        ;

        if ($this->security->isGranted('ROLE_ADMIN')) {
            if ($userFiltering && $userFiltering->getFirstname()) {
                $query
                    ->andWhere('LOWER(u.firstname) LIKE LOWER(:firstname)')
                    ->setParameter('firstname', '%'.$userFiltering->getFirstname().'%')
                ;
            }
            if ($userFiltering && $userFiltering->getLastname()) {
                $query
                    ->andWhere('LOWER(u.lastname) LIKE LOWER(:lastname)')
                    ->setParameter('lastname', '%'.$userFiltering->getLastname().'%')
                ;
            }
            if ($userFiltering && $userFiltering->getEmail()) {
                $query
                    ->andWhere('LOWER(u.email) LIKE LOWER(:email)')
                    ->setParameter('email', '%'.$userFiltering->getEmail().'%')
                ;
            }
            if ($userFiltering && $userFiltering->getRole()) {
                $query
                    ->andWhere('u.roles LIKE :role')
                    ->setParameter('role', '%'.$userFiltering->getRole().'%')
                ;
            }
        }

        return $query->getQuery();
    }
}
