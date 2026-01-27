<?php

namespace App\Tests\Application\Controller\Admin;

use App\Entity\User;
use App\Factory\UserFactory;
use App\Repository\UserRepository;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class UserControllerTest extends WebTestCase
{
    use ResetDatabase;
    use Factories;

    /**
     * Création d'un utilisateur.
     */
    private function createUser(): User
    {
        return UserFactory::createOne([
            'email' => 'user@project.com',
            'roles' => ['ROLE_USER'],
        ]);
    }

    /**
     * Création d'un administrateur.
     */
    private function createAdmin(): User
    {
        return UserFactory::createOne([
            'email' => 'admin@project.com',
            'roles' => ['ROLE_ADMIN'],
        ]);
    }

    /**
     * Retourne le UserRepository.
     */
    private function getUserRepository(): UserRepository
    {
        return static::getContainer()->get(UserRepository::class);
    }

    public static function userProvider(): \Generator
    {
        yield ['Emma', 'Smith', 'emma.smith@project.com'];
        yield ['Paul', 'Jack', 'paul.jack@project.com'];
    }

    /**
     * Vérifie qu'un utilisateur ne peut accéder à la liste des utilisateurs.
     */
    public function testUserCannotList(): void
    {
        $client = static::createClient();
        $user = $this->createUser();
        $client->loginUser($user);

        $client->request('GET', 'admin/user/');

        $this->assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    /**
     * Vérifie qu'un administrateur peut accéder à la liste des utilisateurs.
     */
    public function testAdminCanList(): void
    {
        $client = static::createClient();
        $admin = $this->createAdmin();
        $client->loginUser($admin);

        $client->request('GET', 'admin/user/');

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    /**
     * Vérifie qu'un utilisateur ne peut créer un nouvel utilisateur.
     */
    public function testUserCannotCreate(): void
    {
        $client = static::createClient();
        $user = $this->createUser();
        $client->loginUser($user);

        $client->request('GET', 'admin/user/new');

        $this->assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    /**
     * Vérifie qu'un administrateur peut créer un nouvel utilisateur.
     */
    #[DataProvider('userProvider')]
    public function testAdminCanCreate(string $firstname, string $lastname, string $email): void
    {
        $client = static::createClient();
        $admin = $this->createAdmin();
        $client->loginUser($admin);

        $client->request('GET', 'admin/user/new');
        $client->submitForm('Enregistrer', [
            'user_form[firstname]' => $firstname,
            'user_form[lastname]' => $lastname,
            'user_form[email]' => $email,
            'user_form[password]' => 'password',
        ]);

        $user = $this->getUserRepository()->findOneBy(['email' => $email]);
        $this->assertSame(Response::HTTP_SEE_OTHER, $client->getResponse()->getStatusCode());
        $this->assertSame($firstname, $user->getFirstname());
        $this->assertSame($lastname, $user->getLastname());
        $this->assertSame($email, $user->getEmail());
    }

    /**
     * Vérifie qu'un utilisateur ne peut visualiser un autre utilisateur.
     */
    public function testUserCannotShow(): void
    {
        $client = static::createClient();
        $user = $this->createUser();
        $admin = $this->createAdmin();
        $client->loginUser($user);

        $client->request('GET', "admin/user/{$admin->getId()}/show");

        $this->assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    /**
     * Vérifie qu'un administrateur peut visualiser un autre utilisateur.
     */
    public function testAdminCanShow(): void
    {
        $client = static::createClient();
        $user = $this->createUser();
        $admin = $this->createAdmin();
        $client->loginUser($admin);

        $client->request('GET', "admin/user/{$user->getId()}/show");

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    /**
     * Vérifie qu'un utilisateur ne peut modifier un autre utilisateur.
     */
    public function testUserCannotEdit(): void
    {
        $client = static::createClient();
        $user = $this->createUser();
        $admin = $this->createAdmin();
        $client->loginUser($user);

        $client->request('GET', "admin/user/{$admin->getId()}/edit");

        $this->assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    /**
     * Vérifie qu'un administrateur peut modifier un autre utilisateur.
     */
    #[DataProvider('userProvider')]
    public function testAdminCanEdit(string $firstname, string $lastname, string $email): void
    {
        $client = static::createClient();
        $user = $this->createUser();
        $admin = $this->createAdmin();
        $client->loginUser($admin);

        $client->request('GET', "admin/user/{$user->getId()}/edit");

        $client->submitForm('Enregistrer', [
            'user_form[firstname]' => $firstname,
            'user_form[lastname]' => $lastname,
            'user_form[email]' => $user->getEmail(),
        ]);

        $user = $this->getUserRepository()->findOneBy(['email' => $user->getEmail()]);
        $this->assertSame(Response::HTTP_SEE_OTHER, $client->getResponse()->getStatusCode());
        $this->assertSame($firstname, $user->getFirstname());
        $this->assertSame($lastname, $user->getLastname());
    }

    /**
     * Vérifie qu'un utilisateur ne peut supprimer un autre utilisateur.
     */
    public function testUserCannotDelete(): void
    {
        $client = static::createClient();
        $user = $this->createUser();
        $admin = $this->createAdmin();
        $client->loginUser($user);

        $client->request('POST', "admin/user/{$admin->getId()}/delete");

        $this->assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    /**
     * Vérifie qu'un administrateur peut supprimer un autre utilisateur.
     */
    public function testAdminCanDelete(): void
    {
        $client = static::createClient();
        $user = $this->createUser();
        $admin = $this->createAdmin();
        $client->loginUser($admin);

        $client->request('POST', "admin/user/{$user->getId()}/delete");
        $this->assertSame(Response::HTTP_SEE_OTHER, $client->getResponse()->getStatusCode());
        $this->assertSame(0, $this->getUserRepository()->count(['email' => $user->getEmail()]));
    }
}
