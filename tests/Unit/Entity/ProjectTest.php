<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Project;
use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Exception\InvalidArgumentException;

class ProjectTest extends TestCase
{

    /**
     * Test l'initialisation et la récupération du nom du projet.
     *
     * @return void
     */
    public function testCanSetAndGetName(): void
    {
        $project = new Project();
        $project->setName('test');
        $this->assertEquals('test', $project->getName());

        $project->setName('test2');
        $this->assertNotEquals('test', $project->getName());
    }

    /**
     * Test l'initialisation et la récupération de la description du projet.
     *
     * @return void
     */
    public function testCanSetAndGetDescription(): void
    {
        $project = new Project();
        $project->setDescription('ceci est une description');
        $this->assertEquals('ceci est une description', $project->getDescription());

        $project->setDescription('ceci est une autre description');
        $this->assertNotEquals('ceci est une description', $project->getDescription());
    }

    /**
     * Test l'initialisation et la récupération de l'auteur du projet.
     *
     * @return void
     */
    public function testCanSetAndGetOwner(): void
    {
        $project = new Project();

        $project->setOwner(null);
        $this->assertEmpty($project->getOwner());

        $user = new User();
        $user->setEmail('test@project.com');
        $project->setOwner($user);
        $this->assertNotEmpty($project->getOwner());
        $this->assertSame('test@project.com', $project->getOwner()->getEmail());

        $user->setEmail('test2@project.com');
        $project->setOwner($user);
        $this->assertNotEmpty($project->getOwner());
        $this->assertNotSame('test@project.com', $project->getOwner()->getEmail());
    }

    /**
     * Test l'initialisation et la récupération des tâches du projet.
     *
     * @return void
     */
    public function testCanSetAndGetTasks(): void
    {
        $project = new Project();

        $this->assertEmpty($project->getTasks());

        $task1 = new Task();
        $task1->setName('task 1');
        $project->addTask($task1);
        $this->assertNotEmpty($project->getTasks());
        $this->assertCount(1, $project->getTasks());

        $task2 = new Task();
        $task2->setName('task 2');
        $project->addTask($task2);
        $this->assertCount(2, $project->getTasks());


        $project->removeTask($task2);
        $this->assertCount(1, $project->getTasks());

        $project->removeTask($task1);
        $this->assertCount(0, $project->getTasks());
    }

    /**
     * Test l'initialisation et la récupération des contributeurs du projet.
     *
     * @return void
     */
    public function testCanSetAndGetContributors(): void
    {
        $project = new Project();

        $this->assertEmpty($project->getContributors());

        $user1 = new User();
        $user1->setEmail('user1@project.com');
        $project->addContributor($user1);
        $this->assertNotEmpty($project->getContributors());
        $this->assertCount(1, $project->getContributors());

        $user2 = new User();
        $user2->setEmail('user2@project.com');
        $project->addContributor($user2);
        $this->assertCount(2, $project->getContributors());

        $project->removeContributor($user1);
        $this->assertCount(1, $project->getContributors());

        $project->removeContributor($user2);
        $this->assertCount(0, $project->getContributors());

    }
}
