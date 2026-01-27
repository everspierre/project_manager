<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * Connecte l'utilisateur.
     */
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * Déconnecte l'utilisateur.
     */
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * Enregistre l'utilisateur admin.
     */
    #[Route(path: '/register', name: 'app_register')]
    public function register(UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $admin = new User();
        $admin->setEmail('admin@project.com');
        $admin->setFirstname('Admin');
        $admin->setLastname('Admin');
        $admin->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        $adminPassword = $userPasswordHasher->hashPassword($admin, 'admin');
        $admin->setPassword($adminPassword);
        $entityManager->persist($admin);

        $user = new User();
        $user->setEmail('user@project.com');
        $user->setFirstname('User');
        $user->setLastname('User');
        $admin->setRoles(['ROLE_USER']);
        $userPassword = $userPasswordHasher->hashPassword($user, 'user');
        $user->setPassword($userPassword);
        $entityManager->persist($user);

        $entityManager->flush();

        return $this->redirectToRoute('app_login');
    }
}
