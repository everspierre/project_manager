<?php

namespace App\Entity\Traits;

use App\Entity\File;

trait FileAwareTrait
{
    /**
     * @var File[]
     */
    private iterable $files = [];

    /**
     * Stocke les objets UploadedFiles du formulaire (non mappé en base).
     */
    private ?array $pendingFiles = null;

    /**
     * Retourne les fichiers.
     */
    public function getFiles(): iterable
    {
        return $this->files;
    }

    /**
     * Initialise les fichiers.
     */
    public function setFiles(iterable $files): self
    {
        $this->files = $files;

        return $this;
    }

    /**
     * Retourne les objets UploadedFiles du formulaire (non mappé en base).
     */
    public function getPendingFiles(): ?array
    {
        return $this->pendingFiles;
    }

    /**
     * Initialise les objets UploadedFiles du formulaire (non mappé en base).
     */
    public function setPendingFiles(?array $pendingFiles): self
    {
        $this->pendingFiles = $pendingFiles;

        return $this;
    }
}
