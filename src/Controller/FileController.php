<?php

namespace App\Controller;

use App\Entity\File;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/file')]
class FileController extends AbstractController
{
    /**
     * Suppression d'un fichier.
     */
    #[Route('/{id}/delete', name: 'app_file_delete', methods: ['POST'])]
    public function delete(File $file, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$file->getId(), $request->request->get('_token'))) {
            $entityManager->remove($file);
            $entityManager->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }
}
