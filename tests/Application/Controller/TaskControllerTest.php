<?php

namespace App\Tests\Application\Controller;

use App\Entity\Project;
use App\Entity\Task;
use App\Entity\User;
use App\Factory\ProjectFactory;
use App\Factory\TaskFactory;
use App\Factory\UserFactory;
use App\Repository\TaskRepository;
use Faker\Factory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class TaskControllerTest extends WebTestCase
{
    use ResetDatabase, Factories;

    /**
     * Création d'une tâche.
     *
     * @return Task
     */
    public function createTask(): Task
    {
        return TaskFactory::createOne();
    }

    /**
     * Création d'un projet.
     *
     * @return Project
     */
    private function createProject(): Project
    {
        return ProjectFactory::createOne();
    }

    /**
     * Création d'un utilisateur.
     *
     * @return User
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
     *
     * @return User
     */
    private function createAdmin(): User
    {
        return UserFactory::createOne([
            'email' => 'admin@project.com',
            'roles' => ['ROLE_ADMIN'],
        ]);
    }

    /**
     * Retourne le TaskRepository.
     *
     * @return TaskRepository
     */
    private function getTaskRepository(): TaskRepository
    {
        return static::getContainer()->get(TaskRepository::class);
    }

    /**
     * Vérifie qu'un utilisateur n'étant pas sur le projet ne peut créer une tâche.
     *
     * @return void
     */
    public function testUserCannotCreate(): void
    {
        $client = static::createClient();
        $user = $this->createUser();
        $client->loginUser($user);
        $project = $this->createProject();
        $client->request('GET', "/task/new/{$project->getId()}");
        $this->assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    /**
     * Vérifie qu'un administrateur peut créer une tâche à un projet.
     *
     * @return void
     */
    public function testAdminCanCreate(): void
    {
        $client = static::createClient();
        $admin = $this->createAdmin();
        $client->loginUser($admin);
        $project = $this->createProject();
        $client->request('GET', "/task/new/{$project->getId()}");
        $client->submitForm('Enregistrer', [
            'task[name]' => Factory::create()->sentence(),
            'task[description]' => Factory::create()->text(),
            'task[startingDate]' => Factory::create()->dateTime()->format('Y-m-d'),
            'task[endingDate]' => ''
        ]);
        $this->assertSame(Response::HTTP_SEE_OTHER, $client->getResponse()->getStatusCode());
    }

    /**
     * Vérifie qu'un propriétaire peut créer une tâche à un projet.
     *
     * @return void
     */
    public function testOwnerCanCreate(): void
    {
        $client = static::createClient();
        $project = $this->createProject();
        $client->loginUser($project->getOwner());
        $client->request('GET', "/task/new/{$project->getId()}");
        $client->submitForm('Enregistrer', [
            'task[name]' => Factory::create()->sentence(),
            'task[description]' => Factory::create()->text(),
            'task[startingDate]' => Factory::create()->dateTime()->format('Y-m-d'),
            'task[endingDate]' => ''
        ]);
        $this->assertSame(Response::HTTP_SEE_OTHER, $client->getResponse()->getStatusCode());
    }

    /**
     * Vérifie qu'un contributeur peut créer une tâche à un projet.
     *
     * @return void
     */
    public function testContributorCanCreate(): void
    {
        $client = static::createClient();
        $project = $this->createProject();
        $client->loginUser($project->getContributors()->first());
        $client->request('GET', "/task/new/{$project->getId()}");
        $client->submitForm('Enregistrer', [
            'task[name]' => Factory::create()->sentence(),
            'task[description]' => Factory::create()->text(),
            'task[startingDate]' => Factory::create()->dateTime()->format('Y-m-d'),
            'task[endingDate]' => ''
        ]);
        $this->assertSame(Response::HTTP_SEE_OTHER, $client->getResponse()->getStatusCode());
    }

    /**
     * Vérifie qu'un utilisateur n'étant pas sur le projet ne peut visualiser une tâche.
     *
     * @return void
     */
    public function testUserCannotShow(): void
    {
        $client = static::createClient();
        $user = $this->createUser();
        $client->loginUser($user);
        $project = $this->createProject();
        $client->request('GET', "/task/{$project->getTasks()->first()->getId()}");
        $this->assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    /**
     * Vérifie qu'un administrateur peut visualiser une tâche à un projet.
     *
     * @return void
     */
    public function testAdminCanShow(): void
    {
        $client = static::createClient();
        $admin = $this->createAdmin();
        $client->loginUser($admin);
        $project = $this->createProject();
        $client->request('GET', "/task/{$project->getTasks()->first()->getId()}");
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    /**
     * Vérifie qu'un propriétaire peut visualiser une tâche à un projet.
     *
     * @return void
     */
    public function testOwnerCanShow(): void
    {
        $client = static::createClient();
        $project = $this->createProject();
        $client->loginUser($project->getOwner());
        $client->request('GET', "/task/{$project->getTasks()->first()->getId()}");
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    /**
     * Vérifie qu'un contributeur peut visualiser une tâche à un projet.
     *
     * @return void
     */
    public function testContributorCanShow(): void
    {
        $client = static::createClient();
        $project = $this->createProject();
        $client->loginUser($project->getContributors()->first());
        $client->request('GET', "/task/{$project->getTasks()->first()->getId()}");
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    /**
     * Vérifie qu'un utilisateur ne peut pas modifier une tâche d'un projet.
     *
     * @return void
     */
    public function testUserCannotEdit(): void
    {
        $client = static::createClient();
        $user = $this->createUser();
        $client->loginUser($user);
        $project = $this->createProject();
        $client->request('GET', "/task/{$project->getTasks()->first()->getId()}/edit");
        $this->assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    /**
     * Vérifie qu'un administrateur peut modifier une tâche d'un projet.
     *
     * @return void
     */
    public function testAdminCanEdit(): void
    {
        $client = static::createClient();
        $admin = $this->createAdmin();
        $client->loginUser($admin);
        $project = $this->createProject();
        $client->request('GET', "/task/{$project->getTasks()->first()->getId()}/edit");
        $newName = Factory::create()->sentence();
        $client->submitForm('Enregistrer', [
            'task[name]' => $newName
        ]);
        $this->assertSame(Response::HTTP_SEE_OTHER, $client->getResponse()->getStatusCode());
        $this->assertSame($newName, $this->getTaskRepository()->findOneById($project->getTasks()->first()->getId())->getName());
    }

    /**
     * Vérifie qu'un propriétaire peut modifier une tâche d'un projet.
     *
     * @return void
     */
    public function testOwnerCanEdit(): void
    {
        $client = static::createClient();
        $project = $this->createProject();
        $client->loginUser($project->getOwner());
        $client->request('GET', "/task/{$project->getTasks()->first()->getId()}/edit");
        $newName = Factory::create()->sentence();
        $client->submitForm('Enregistrer', [
            'task[name]' => $newName
        ]);
        $this->assertSame(Response::HTTP_SEE_OTHER, $client->getResponse()->getStatusCode());
        $this->assertSame($newName, $this->getTaskRepository()->findOneById($project->getTasks()->first()->getId())->getName());
    }

    /**
     * Vérifie qu'un contributeur peut modifier une tâche d'un projet.
     *
     * @return void
     */
    public function testContributorCanEdit(): void
    {
        $client = static::createClient();
        $project = $this->createProject();
        $client->loginUser($project->getContributors()->first());
        $client->request('GET', "/task/{$project->getTasks()->first()->getId()}/edit");
        $newName = Factory::create()->sentence();
        $client->submitForm('Enregistrer', [
            'task[name]' => $newName
        ]);
        $this->assertSame(Response::HTTP_SEE_OTHER, $client->getResponse()->getStatusCode());
        $this->assertSame($newName, $this->getTaskRepository()->findOneById($project->getTasks()->first()->getId())->getName());
    }

    /**
     * Vérifie qu'un utilisateur n'étant pas sur le projet ne peut pas supprimer une tâche.
     *
     * @return void
     */
    public function testUserCannotDelete(): void
    {
        $client = static::createClient();
        $user = $this->createUser();
        $client->loginUser($user);
        $project = $this->createProject();
        $client->request('POST', "/task/{$project->getTasks()->first()->getId()}/delete");
        $this->assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    /**
     * Vérifie qu'un administrateur peut supprimer une tâche à un projet.
     *
     * @return void
     */
    public function testAdminCanDelete(): void
    {
        $client = static::createClient();
        $admin = $this->createAdmin();
        $client->loginUser($admin);
        $project = $this->createProject();
        $client->request('POST', "/task/{$project->getTasks()->first()->getId()}/delete");
        $this->assertSame(Response::HTTP_SEE_OTHER, $client->getResponse()->getStatusCode());
    }

    /**
     * Vérifie qu'un propriétaire peut supprimer une tâche à un projet.
     *
     * @return void
     */
    public function testOwnerCanDelete(): void
    {
        $client = static::createClient();
        $project = $this->createProject();
        $client->loginUser($project->getOwner());
        $client->request('POST', "/task/{$project->getTasks()->first()->getId()}/delete");
        $this->assertSame(Response::HTTP_SEE_OTHER, $client->getResponse()->getStatusCode());
    }

    /**
     * Vérifie qu'un contributeur peut supprimer une tâche à un projet.
     *
     * @return void
     */
    public function testContributorCanDelete(): void
    {
        $client = static::createClient();
        $project = $this->createProject();
        $client->loginUser($project->getContributors()->first());
        $client->request('POST', "/task/{$project->getTasks()->first()->getId()}/delete");
        $this->assertSame(Response::HTTP_SEE_OTHER, $client->getResponse()->getStatusCode());
    }

}
