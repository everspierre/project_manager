<?php

namespace App\EventListener\FileListener;

use App\Service\File\FileUploaderService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

#[AsDoctrineListener(event: Events::postPersist)]
#[AsDoctrineListener(event: Events::onFlush)]
#[AsDoctrineListener(event: Events::postFlush)]
class FileAutomatorListener
{
    /**
     * Liste des entités qui ont été traitées.
     */
    private array $processedEntities = [];

    public function __construct(private FileUploaderService $fileUploader)
    {
    }

    /**
     * Traitement des fichiers pour les nouvelles entités.
     */
    public function postPersist(LifecycleEventArgs $args): void
    {
        $this->process($args->getObject());
    }

    /**
     * Traitement des fichiers après mise à jour des entités.
     */
    public function onFlush(OnFlushEventArgs $args): void
    {
        $em = $args->getObjectManager();
        $uow = $em->getUnitOfWork();

        // L'IdentityMap contient TOUTES les entités chargées dans cette requête
        foreach ($uow->getIdentityMap() as $className => $entities) {
            foreach ($entities as $entity) {
                // On vérifie si l'entité a des fichiers et si elle a déjà un ID
                // (Si elle n'a pas d'ID, c'est une création, postPersist s'en chargera)
                if (method_exists($entity, 'getPendingFiles') && null !== $entity->getId()) {
                    $this->process($entity);
                }
            }
        }
    }

    /**
     * Suppression de la liste des entités traitées après mise à jour des entités.
     */
    public function postFlush(PostFlushEventArgs $args): void
    {
        $this->processedEntities = [];
    }

    /**
     * Traite les fichiers en attente et les associe à l'entité.
     */
    public function process(object $entity): void
    {
        $oid = spl_object_hash($entity);
        if (isset($this->processedEntities[$oid])) {
            return;
        }

        if (method_exists($entity, 'getPendingFiles')) {
            $files = $entity->getPendingFiles();
            if ($files && count($files) > 0) {
                $this->processedEntities[$oid] = true;
                $this->fileUploader->uploadMultiple($entity, $files);
                $entity->setPendingFiles(null);
            }
        }
    }
}
