<?php

namespace App\Tests\Integration\Repository;

use App\Factory\ProjectFactory;
use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class ProjectRepositoryTest extends KernelTestCase
{
    use ResetDatabase, Factories;

    /**
     * Retourne le ProjectRepository
     *
     * @return ProjectRepository
     */
    private function getProjectRepository(): ProjectRepository
    {
        return self::getContainer()->get(ProjectRepository::class);
    }

    /**
     * Test la création de plusieurs projets.
     *
     * @return void
     */
    public function testProjectsAreCreated(): void
    {
        self::bootKernel();

        ProjectFactory::createMany(5);

        $this->assertSame(5, $this->getProjectRepository()->count());
    }

}
