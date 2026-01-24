<?php

namespace App\Tests\Integration\Repository;

use App\Factory\ProjectFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class TaskRepositoryTest extends KernelTestCase
{
    use ResetDatabase, Factories;

    /**
     * Test la création de plusieurs tâches.
     *
     * @return void
     */
    public function testTasksAreCreated(): void
    {
        self::bootKernel();

        $project = ProjectFactory::createOne();

        $this->assertNotEquals(0, $project->getTasks()->count());
    }

}
