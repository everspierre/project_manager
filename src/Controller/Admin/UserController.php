<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserForm;
use App\Form\UserPasswordForm;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/user')]
#[IsGranted("ROLE_ADMIN")]
class UserController extends AbstractController
{
    /**
     * Listing des utilisateurs.
     *
     * @param UserRepository $userRepository
     *
     * @return Response
     */
    #[Route(name: 'app_admin_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('admin/user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * Création d'un nouvel utilisateur.
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     *
     * @return Response
     */
    #[Route(path: '/new', name: 'app_admin_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $user->setRoles(['ROLE_USER']);
        $form = $this->createForm(UserForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_user_index', [],  Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/user/new.html.twig', [
            'form' => $form,
            'user' => $user,
        ]);
    }

    /**
     * Visualisation d'un utilisateur.
     *
     * @param User $user
     *
     * @return Response
     */
    #[Route(path: '/{id}/show', name: 'app_admin_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('admin/user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * Mise à jour d'un utilisateur.
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param User $user
     *
     * @return Response
     */
    #[Route(path: '/{id}/edit', name: 'app_admin_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, User $user): Response
    {
        $form = $this->createForm(UserForm::class, $user, ['disable_password' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_user_show', ['id' => $user->getId()],  Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/user/edit.html.twig', [
            'form' => $form,
            'user' => $user,
        ]);
    }

    /**
     * Mise à jour du mot de passe de l'utilisateur.
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param User $user
     *
     * @return Response
     */
    #[Route(path: '/{id}/edit-password', name: 'app_admin_user_edit_password', methods: ['GET', 'POST'])]
    public function editPassword(Request $request, EntityManagerInterface $entityManager, User $user): Response
    {
        $form = $this->createForm(UserPasswordForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_user_show', ['id' => $user->getId()],  Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/user/edit_password.html.twig', [
            'form' => $form,
            'user' => $user,
        ]);
    }

    /**
     * Suppression d'un utilisateur.
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param User $user
     *
     * @return Response
     */
    #[Route(path: '/{id}/delete', name: 'app_admin_user_delete', methods: ['POST'])]
    public function delete(Request $request, EntityManagerInterface $entityManager, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
    }

}
