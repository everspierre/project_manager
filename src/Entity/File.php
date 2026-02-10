<?php

namespace App\Entity;

use App\Repository\FileRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File as HttpFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: FileRepository::class)]
#[ORM\Index(name: 'idx_file_owner', columns: ['owner_id', 'owner_type'])]
#[Vich\Uploadable]
class File
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Vich\UploadableField(mapping: 'general_file', fileNameProperty: 'filename', size: 'size')]
    private ?HttpFile $file = null;

    #[ORM\Column(length: 255)]
    private ?string $filename = null;

    #[ORM\Column]
    private ?int $size = null;

    #[ORM\Column]
    private int $ownerId;

    #[ORM\Column(length: 50)]
    private string $ownerType;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Retourne le fichier.
     */
    public function getFile(): ?HttpFile
    {
        return $this->file;
    }

    /**
     * Initialise le fichier.
     */
    public function setFile(?HttpFile $file = null): static
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Retourne le nom du fichier.
     */
    public function getFilename(): ?string
    {
        return $this->filename;
    }

    /**
     * Initialise le nom du fichier.
     */
    public function setFilename(?string $filename): static
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Retourne la taille du fichier.
     */
    public function getSize(): ?int
    {
        return $this->size;
    }

    /**
     * Initialise la taille du fichier.
     */
    public function setSize(?int $size): static
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Retourne l'identifiant de l'entité associée.
     */
    public function getOwnerId(): int
    {
        return $this->ownerId;
    }

    /**
     * Initialise l'identifiant de l'entité associée.
     */
    public function setOwnerId(int $ownerId): static
    {
        $this->ownerId = $ownerId;

        return $this;
    }

    /**
     * Retourne le type de l'entité associée.
     */
    public function getOwnerType(): string
    {
        return $this->ownerType;
    }

    /**
     * Initialise le type de l'entité associée.
     */
    public function setOwnerType(string $ownerType): static
    {
        $this->ownerType = $ownerType;

        return $this;
    }
}
