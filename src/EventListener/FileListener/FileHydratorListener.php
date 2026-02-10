<?php

namespace App\EventListener\FileListener;

use App\Repository\FileRepository;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

#[AsDoctrineListener(event: Events::postLoad)]
class FileHydratorListener
{
    public function __construct(private FileRepository $fileRepository)
    {
    }

    /**
     * Charge automatiquement les fichiers associés à l'entité.
     */
    public function postLoad(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (method_exists($entity, 'setFiles')) {
            $files = $this->fileRepository->findFilesFor($entity);
            $entity->setFiles($files);
        }
    }
}
