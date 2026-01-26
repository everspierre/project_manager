<?php

namespace App\Tests\Integration\Repository;

use App\Factory\UserFactory;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class UserRepositoryTest extends KernelTestCase
{
    use ResetDatabase;
    use Factories;

    /**
     * Retourne le UserRepository.
     */
    private function getUserRepository(): UserRepository
    {
        return self::getContainer()->get(UserRepository::class);
    }

    /**
     * Test la création de plusieurs utilisateurs.
     */
    public function testUsersAreCreated(): void
    {
        self::bootKernel();

        UserFactory::createMany(5);

        $this->assertSame(5, $this->getUserRepository()->count());
    }
}
