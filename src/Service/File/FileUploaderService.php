<?php

namespace App\Service\File;

use App\Entity\File;
use Doctrine\ORM\EntityManagerInterface;

class FileUploaderService
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    /**
     * Attache plusieurs fichiers à n'importe quelle entité.
     */
    public function uploadMultiple(object $owner, ?array $uploadedFiles): void
    {
        if (!$uploadedFiles || 0 === count($uploadedFiles)) {
            return;
        }

        foreach ($uploadedFiles as $uploadedFile) {
            if (!$uploadedFile->isValid()) {
                continue;
            }
            $file = new File();
            $file->setFile($uploadedFile);
            $file->setOwnerId($owner->getId());
            $file->setOwnerType(strtolower((new \ReflectionClass($owner))->getShortName()));

            $this->em->persist($file);
        }

        $this->em->flush();
    }
}
