<?php

namespace App\Repository;

use App\Entity\File;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<File>
 */
class FileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, File::class);
    }

    /**
     * Retourne les fichiers associés à une entité.
     */
    public function findFilesFor(object $owner): array
    {
        $class = new \ReflectionClass($owner);
        $ownerType = strtolower($class->getShortName());

        return $this->createQueryBuilder('f')
            ->andWhere('f.ownerId = :id')
            ->andWhere('f.ownerType = :type')
            ->setParameter('id', $owner->getId())
            ->setParameter('type', $ownerType)
            ->getQuery()
            ->getResult()
        ;
    }
}
