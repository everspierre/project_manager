<?php

namespace App\Repository;

use App\Entity\Project;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @extends ServiceEntityRepository<Project>
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private Security $security)
    {
        parent::__construct($registry, Project::class);
    }

    /**
     * Retourne les projects associés à l'utilisateur connecté (propriétaire ou contributeur).
     *
     * @return array
     */
    public function findByUser(): array
    {
        $query = $this->createQueryBuilder('p')
            ->orderBy('p.id', 'DESC')
        ;

        if (!$this->security->isGranted('ROLE_ADMIN')) {
            $query
                ->innerJoin('p.contributors', 'u')
                ->andWhere('u.id = :id')
                ->setParameter('id', $this->security->getUser()->getId())
            ;
        }

        return $query->getQuery()->getResult();
    }
}
