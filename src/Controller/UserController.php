<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserForm;
use App\Form\UserPasswordForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/user')]
#[IsGranted('ROLE_USER')]
class UserController extends AbstractController
{
    /**
     * Visualisation d'un utilisateur.
     */
    #[Route(path: '/{id}/show', name: 'app_user_show', methods: ['GET'])]
    #[IsGranted('view', 'user')]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * Mise à jour d'un utilisateur.
     */
    #[Route(path: '/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    #[IsGranted('edit', 'user')]
    public function edit(Request $request, EntityManagerInterface $entityManager, User $user): Response
    {
        $form = $this->createForm(UserForm::class, $user, [
            'disable_email' => true,
            'disable_roles' => true,
            'disable_password' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form,
            'user' => $user,
        ]);
    }

    /**
     * Mise à jour du mot de passe de l'utilisateur.
     */
    #[Route(path: '/{id}/edit-password', name: 'app_user_edit_password', methods: ['GET', 'POST'])]
    #[IsGranted('edit', 'user')]
    public function editPassword(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, User $user): Response
    {
        $form = $this->createForm(UserPasswordForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);
            $entityManager->flush();

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit_password.html.twig', [
            'form' => $form,
            'user' => $user,
        ]);
    }
}
