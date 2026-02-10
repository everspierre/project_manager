<?php

namespace App\EventListener\FileListener;

use App\Entity\Traits\FileAwareTrait;
use App\Repository\FileRepository;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

#[AsDoctrineListener(event: Events::preRemove)]
class FileCleanupListener
{
    public function __construct(private FileRepository $fileRepository, private EntityManagerInterface $em)
    {
    }

    /**
     * Suppression des fichiers lors de la suppression de l'entité.
     */
    public function preRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if (in_array(FileAwareTrait::class, array_keys((new \ReflectionClass($entity))->getTraits()))) {
            $files = $this->fileRepository->findFilesFor($entity);

            foreach ($files as $file) {
                $this->em->remove($file);
            }
        }
    }
}
