<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\User;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/project')]
#[IsGranted('ROLE_USER')]
final class ProjectController extends AbstractController
{
    /**
     * Listing des projets.
     *
     * @param ProjectRepository $projectRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     *
     * @return Response
     */
    #[Route(name: 'app_project_index', methods: ['GET'])]
    public function index(ProjectRepository $projectRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $projects = $paginator->paginate(
            $projectRepository->findByUser(),
            $request->query->getInt('page', 1),
        );

        $projects->setCustomParameters(['align' => 'right']);

        return $this->render('project/index.html.twig', [
            'projects' => $projects,
        ]);
    }

    /**
     * Création d'un projet.
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     *
     * @return Response
     */
    #[Route('/new', name: 'app_project_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $project = new Project();
        $project->setOwner($this->getUser());
        $project->addContributor($this->getUser());
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($project);
            $entityManager->flush();

            return $this->redirectToRoute('app_project_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('project/new.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }

    /**
     * Détail d'un projet.
     *
     * @param Project $project
     *
     * @return Response
     */
    #[Route('/{id}', name: 'app_project_show', methods: ['GET'])]
    #[IsGranted('view', 'project')]
    public function show(Project $project): Response
    {
        return $this->render('project/show.html.twig', [
            'project' => $project,
        ]);
    }

    /**
     * Mise à jour d'un projet.
     *
     * @param Request $request
     * @param Project $project
     * @param EntityManagerInterface $entityManager
     *
     * @return Response
     */
    #[Route('/{id}/edit', name: 'app_project_edit', methods: ['GET', 'POST'])]
    #[IsGranted('edit', 'project')]
    public function edit(Request $request, Project $project, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_project_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('project/edit.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }

    /**
     * Suppression d'un projet.
     *
     * @param Request $request
     * @param Project $project
     * @param EntityManagerInterface $entityManager
     *
     * @return Response
     */
    #[Route('/{id}', name: 'app_project_delete', methods: ['POST'])]
    #[IsGranted('edit', 'project')]
    public function delete(Request $request, Project $project, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$project->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($project);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_project_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Suppression d'un contributeur.
     *
     * @param Request $request
     * @param Project $project
     * @param User $contributor
     * @param EntityManagerInterface $entityManager
     *
     * @return Response
     */
    #[Route('/{id}/{user_id}', name: 'app_project_delete_contributor', methods: ['POST'])]
    #[IsGranted('edit', 'project')]
    public function deleteContributor(Request $request, Project $project, #[MapEntity(id: 'user_id')] User $contributor,EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$project->getId(), $request->getPayload()->getString('_token'))
            && !$project->isOwner($contributor)
            && $project->isContributor($contributor)
        ) {
            $project->removeContributor($contributor);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_project_show', ['id' => $project->getId()], Response::HTTP_SEE_OTHER);
    }
}
