<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Task;
use App\Form\TaskType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/task')]
#[IsGranted('ROLE_USER')]
final class TaskController extends AbstractController
{
    /**
     * Création d'une tâche.
     *
     * @param Request $request
     * @param Project $project
     * @param EntityManagerInterface $entityManager
     *
     * @return Response
     */
    #[Route('/new/{project_id}', name: 'app_task_new', methods: ['GET', 'POST'])]
    #[IsGranted('create_task', 'project')]
    public function new(Request $request, #[MapEntity(id: 'project_id')] Project $project, EntityManagerInterface $entityManager): Response
    {
        $task = new Task();
        $task->setProject($project);
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($task);
            $entityManager->flush();

            return $this->redirectToRoute('app_project_show', ['id' => $project->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('task/new.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    /**
     * Détail d'une tâche.
     *
     * @param Task $task
     *
     * @return Response
     */
    #[Route('/{id}', name: 'app_task_show', methods: ['GET'])]
    #[IsGranted('view', 'task')]
    public function show(Task $task): Response
    {
        return $this->render('task/show.html.twig', [
            'task' => $task,
        ]);
    }

    /**
     * Mise à jour d'une tâche.
     *
     * @param Request $request
     * @param Task $task
     * @param EntityManagerInterface $entityManager
     *
     * @return Response
     */
    #[Route('/{id}/edit', name: 'app_task_edit', methods: ['GET', 'POST'])]
    #[IsGranted('edit', 'task')]
    public function edit(Request $request, Task $task, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_project_show', [
                'id' => $task->getProject()->getId(),
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('task/edit.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    /**
     * Suppression d'une tâche.
     *
     * @param Request $request
     * @param Task $task
     * @param EntityManagerInterface $entityManager
     *
     * @return Response
     */
    #[Route('/{id}', name: 'app_task_delete', methods: ['POST'])]
    #[IsGranted('edit', 'task')]
    public function delete(Request $request, Task $task, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$task->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($task);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_project_show', ['id' => $task->getProject()->getId()], Response::HTTP_SEE_OTHER);
    }
}
