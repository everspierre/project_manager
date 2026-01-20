<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Faker\Factory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{

    /**
     * Vérifie qu'un utilisateur peut visualiser son profil.
     *
     * @return void
     */
    public function testUserCanShowItself(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'user@project.com']);
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
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'user@project.com']);
        $client->loginUser($user);
        $client->request('GET', "/user/{$user->getId()}/edit");
        $faker = Factory::create();
        $firstname = $faker->firstName();
        $lastname = $faker->lastName();
        $client->submitForm('Enregistrer', [
            'user_form[firstname]' => $firstname,
            'user_form[lastname]' => $lastname,
        ]);

        $user = $userRepository->findOneBy(['email' => 'user@project.com']);

        $this->assertSame(Response::HTTP_SEE_OTHER, $client->getResponse()->getStatusCode());
        $this->assertSame($firstname, $user->getFirstname());
        $this->assertSame($lastname, $user->getLastname());
    }
}
