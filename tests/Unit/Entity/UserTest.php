<?php

namespace App\Tests\Unit\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Exception\InvalidArgumentException;

class UserTest extends TestCase
{

    /**
     * Test l'initialisation et la récupération du prénom de l'utilisateur.
     *
     * @return void
     */
    public function testCanSetAndGetFirstname(): void
    {
        $user = new User();
        $user->setFirstname('test');
        $this->assertEquals('test', $user->getFirstname());

        $user->setFirstname('test2');
        $this->assertNotEquals('test', $user->getFirstname());
    }

    /**
     * Test l'initialisation et la récupération du nom de l'utilisateur.
     *
     * @return void
     */
    public function testCanSetAndGetLastname(): void
    {
        $user = new User();
        $user->setLastname('test');
        $this->assertEquals('test', $user->getLastname());

        $user->setLastname('');
        $this->assertEmpty($user->getLastname());

        $user->setLastname('test2');
        $this->assertNotEquals('test', $user->getLastname());
    }

    /**
     * Test l'initialisation et la récupération de l'email de l'utilisateur.
     *
     * @return void
     */
    public function testCanSetAndGetEmail(): void
    {
        $user = new User();
        $user->setEmail('test@test.test');
        $this->assertEquals('test@test.test', $user->getEmail());

        $this->expectException(InvalidArgumentException::class);
        $user->setEmail('test@test');

        $user->setEmail('');
        $this->assertEmpty($user->getEmail());

        $user->setEmail('test2@test.test');
        $this->assertNotEquals('test@test.test', $user->getEmail());
    }

    /**
     * Test l'initialisation et la récupération des rôles de l'utilisateur.
     *
     * @return void
     */
    public function testCanSetAndGetRoles(): void
    {
        $user = new User();
        $user->setRoles(['ROLE_USER']);
        $this->assertEquals(['ROLE_USER'], $user->getRoles());

        $user->setRoles([]);
        $this->assertNotEmpty($user->getRoles());

        $user->setRoles(['ROLE_ADMIN']);
        $this->assertContains('ROLE_ADMIN', $user->getRoles());

        $user->setRoles(['ROLE_USER']);
        $this->assertNotEquals(['ROLE_ADMIN'], $user->getRoles());
    }
}
