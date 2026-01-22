<?php

namespace App\Tests\Application\Controller;

use App\Entity\User;
use App\Factory\UserFactory;
use App\Repository\UserRepository;
use Faker\Factory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class UserControllerTest extends WebTestCase
{
    use ResetDatabase, Factories;

    /**
     * Création d'un utilisateur.
     *
     * @return User
     */
    private function createUser(): User
    {
        return UserFactory::createOne([
            'email' => 'user@project.com'
        ]);
    }

    /**
     * Retourne le UserRepository.
     *
     * @return UserRepository
     */
    private function getUserRepository(): UserRepository
    {
        return static::getContainer()->get(UserRepository::class);
    }

    /**
     * Vérifie qu'un utilisateur peut visualiser son profil.
     *
     * @return void
     */
    public function testUserCanShowItself(): void
    {
        $client = static::createClient();
        $user = $this->createUser();
        $client->loginUser($user);

        $client->request('GET', "/user/{$user->getId()}/show");

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Utilisateur');
    }

    /**
     * Vérifie qu'un utilisateur peut modifier son profil.
     *
     * @return void
     */
    public function testUserCanEditItself(): void
    {
        $client = static::createClient();
        $user = $this->createUser();
        $client->loginUser($user);
        $client->request('GET', "/user/{$user->getId()}/edit");
        $faker = Factory::create();
        $firstname = $faker->firstName();
        $lastname = $faker->lastName();
        $client->submitForm('Enregistrer', [
            'user_form[firstname]' => $firstname,
            'user_form[lastname]' => $lastname,
        ]);

        $userRepository = $this->getUserRepository();
        $user = $userRepository->findOneBy(['email' => 'user@project.com']);

        $this->assertSame(Response::HTTP_SEE_OTHER, $client->getResponse()->getStatusCode());
        $this->assertSame($firstname, $user->getFirstname());
        $this->assertSame($lastname, $user->getLastname());
    }
}
