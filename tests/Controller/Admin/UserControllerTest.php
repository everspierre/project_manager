<?php

namespace App\Tests\Controller\Admin;

use App\Repository\UserRepository;
use Faker\Factory;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{
    /**
     * @return Generator
     */
    public static function userProvider(): Generator
    {
        yield ['Emma', 'Smith', 'emma.smith@project.com'];
        yield ['Paul', 'Jack', 'paul.jack@project.com'];
    }

    /**
     * Vérifie qu'un utilisateur ne peut accéder à la liste des utilisateurs.
     *
     * @return void
     */
    public function testUserCannotList(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'user@project.com']);
        $client->loginUser($user);

        $client->request('GET', "admin/user/");

        $this->assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    /**
     * Vérifie qu'un administrateur peut accéder à la liste des utilisateurs.
     *
     * @return void
     */
    public function testAdminCanList(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $admin = $userRepository->findOneBy(['email' => 'admin@project.com']);
        $client->loginUser($admin);

        $client->request('GET', "admin/user/");

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    /**
     * Vérifie qu'un utilisateur ne peut créer un nouvel utilisateur.
     *
     * @return void
     */
    public function testUserCannotCreate(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'user@project.com']);
        $client->loginUser($user);

        $client->request('GET', "admin/user/new");

        $this->assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    /**
     * Vérifie qu'un administrateur peut créer un nouvel utilisateur.
     *
     * @param string $firstname
     * @param string $lastname
     * @param string $email
     *
     * @return void
     */
    #[DataProvider('userProvider')]
    public function testAdminCanCreate(string $firstname, string $lastname, string $email): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $admin = $userRepository->findOneBy(['email' => 'admin@project.com']);
        $client->loginUser($admin);

        $client->request('GET', "admin/user/new");
        $client->submitForm('Enregistrer', [
            'user_form[firstname]' => $firstname,
            'user_form[lastname]' => $lastname,
            'user_form[email]' => $email,
            'user_form[password]' => 'password',
        ]);

        $user = $userRepository->findOneBy(['email' => $email]);
        $this->assertSame(Response::HTTP_SEE_OTHER, $client->getResponse()->getStatusCode());
        $this->assertSame($firstname, $user->getFirstname());
        $this->assertSame($lastname, $user->getLastname());
        $this->assertSame($email, $user->getEmail());
    }

    /**
     * Vérifie qu'un utilisateur ne peut visualiser un autre utilisateur.
     *
     * @return void
     */
    public function testUserCannotShow(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'user@project.com']);
        $admin = $userRepository->findOneBy(['email' => 'admin@project.com']);
        $client->loginUser($user);

        $client->request('GET', "admin/user/{$admin->getId()}/show");

        $this->assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    /**
     * Vérifie qu'un administrateur peut visualiser un autre utilisateur.
     *
     * @param string $firstname
     * @param string $lastname
     * @param string $email
     *
     * @return void
     */
    #[DataProvider('userProvider')]
    public function testAdminCanShow(string $firstname, string $lastname, string $email): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => $email]);
        $admin = $userRepository->findOneBy(['email' => 'admin@project.com']);
        $client->loginUser($admin);

        $client->request('GET', "admin/user/{$user->getId()}/show");

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    /**
     * Vérifie qu'un utilisateur ne peut modifier un autre utilisateur.
     *
     * @return void
     */
    public function testUserCannotEdit(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'user@project.com']);
        $admin = $userRepository->findOneBy(['email' => 'admin@project.com']);
        $client->loginUser($user);

        $client->request('GET', "admin/user/{$admin->getId()}/edit");

        $this->assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    /**
     * Vérifie qu'un administrateur peut modifier un autre utilisateur.
     *
     * @param string $firstname
     * @param string $lastname
     * @param string $email
     *
     * @return void
     */
    #[DataProvider('userProvider')]
    public function testAdminCanEdit(string $firstname, string $lastname, string $email): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $admin = $userRepository->findOneBy(['email' => 'admin@project.com']);
        $user = $userRepository->findOneBy(['email' => $email]);
        $client->loginUser($admin);

        $client->request('GET', "admin/user/{$user->getId()}/edit");

        $faker = Factory::create();
        $firstname = $faker->firstName();
        $lastname = $faker->lastName();

        $client->submitForm('Enregistrer', [
            'user_form[firstname]' => $firstname,
            'user_form[lastname]' => $lastname,
            'user_form[email]' => $user->getEmail(),
        ]);

        $user = $userRepository->findOneBy(['email' => $email]);
        $this->assertSame(Response::HTTP_SEE_OTHER, $client->getResponse()->getStatusCode());
        $this->assertSame($firstname, $user->getFirstname());
        $this->assertSame($lastname, $user->getLastname());
    }

    /**
     * Vérifie qu'un utilisateur ne peut supprimer un autre utilisateur.
     *
     * @return void
     */
    public function testUserCannotDelete(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'user@project.com']);
        $admin = $userRepository->findOneBy(['email' => 'admin@project.com']);
        $client->loginUser($user);

        $client->request('POST', "admin/user/{$admin->getId()}/delete");

        $this->assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    /**
     * Vérifie qu'un administrateur peut supprimer un autre utilisateur.
     *
     * @param string $firstname
     * @param string $lastname
     * @param string $email
     *
     * @return void
     */
    #[DataProvider('userProvider')]
    public function testAdminCanDelete(string $firstname, string $lastname, string $email): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $admin = $userRepository->findOneBy(['email' => 'admin@project.com']);
        $user = $userRepository->findOneBy(['email' => $email]);
        $client->loginUser($admin);

        $client->request('POST', "admin/user/{$user->getId()}/delete");
        $this->assertSame(Response::HTTP_SEE_OTHER, $client->getResponse()->getStatusCode());
        $this->assertSame(0, $userRepository->count(['email' => $email]));
    }
}
