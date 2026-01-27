<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Project;
use App\Entity\Task;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Exception\InvalidArgumentException;

class TaskTest extends TestCase
{
    /**
     * Test l'initialisation et la récupération du nom de la tâche.
     */
    public function testCanSetAndGetName(): void
    {
        $task = new Task();

        $this->assertNull($task->getName());

        $task->setName('test');
        $this->assertEquals('test', $task->getName());

        $task->setName('test2');
        $this->assertNotEquals('test', $task->getName());
    }

    /**
     * Test l'initialisation et la récupération de la description de la tâche.
     */
    public function testCanSetAndGetDescription(): void
    {
        $task = new Task();

        $this->assertNull($task->getDescription());

        $task->setDescription('ceci est une description');
        $this->assertEquals('ceci est une description', $task->getDescription());

        $task->setDescription('ceci est une autre description');
        $this->assertNotEquals('ceci est une description', $task->getDescription());
    }

    /**
     * Test l'initialisation et la récupération de la date de début de la tâche.
     */
    public function testCanSetAndGetStartingDate(): void
    {
        $task = new Task();

        $this->assertNull($task->getStartingDate());

        $task->setStartingDate(\DateTime::createFromFormat('Y-m-d', '2019-01-01'));
        $this->assertEquals(\DateTime::createFromFormat('Y-m-d', '2019-01-01'), $task->getStartingDate());

        $task->setStartingDate(new \DateTime('2023-01-01'));
        $this->assertNotEquals(new \DateTime(), $task->getStartingDate());
    }

    /**
     * Test l'initialisation et la récupération de la date de fin de la tâche.
     */
    public function testCanSetAndGetEndingDate(): void
    {
        $task = new Task();

        $this->assertNull($task->getEndingDate());

        $this->expectException(InvalidArgumentException::class);
        $task->setEndingDate(\DateTime::createFromFormat('Y-m-d', '2018-01-01'));

        $task->setStartingDate(\DateTime::createFromFormat('Y-m-d', '2019-01-01'));

        $task->setEndingDate(\DateTime::createFromFormat('Y-m-d', '2019-02-01'));
        $this->assertEquals(\DateTime::createFromFormat('Y-m-d', '2019-02-01'), $task->getEndingDate());

        $task->setEndingDate(new \DateTime('2023-01-01'));
        $this->assertNotEquals(new \DateTime(), $task->getEndingDate());

        $this->expectException(InvalidArgumentException::class);
        $task->setEndingDate(\DateTime::createFromFormat('Y-m-d', '2018-01-01'));
    }

    /**
     * Test l'initialisation et la récupération du projet.
     */
    public function testCanSetAndGetProject(): void
    {
        $project = new Project();
        $task = new Task();

        $this->assertNull($task->getProject());

        $task->setProject($project);
        $this->assertSame($project, $task->getProject());

        $otherProject = new Project();
        $task->setProject($otherProject);
        $this->assertNotSame($project, $task->getProject());
    }
}
