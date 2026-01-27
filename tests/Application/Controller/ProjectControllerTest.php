<?php

namespace App\Tests\Application\Controller;

use App\Entity\Project;
use App\Entity\User;
use App\Factory\ProjectFactory;
use App\Factory\UserFactory;
use App\Repository\ProjectRepository;
use Faker\Factory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class ProjectControllerTest extends WebTestCase
{
    use ResetDatabase;
    use Factories;

    /**
     * Création d'un projet.
     */
    private function createProject(): Project
    {
        return ProjectFactory::createOne();
    }

    /**
     * Création d'un utilisateur.
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
     */
    private function createAdmin(): User
    {
        return UserFactory::createOne([
            'email' => 'admin@project.com',
            'roles' => ['ROLE_ADMIN'],
        ]);
    }

    /**
     * Retourne le ProjectRepository.
     */
    private function getProjectRepository(): ProjectRepository
    {
        return static::getContainer()->get(ProjectRepository::class);
    }

    /**
     * Vérifie qu'un utilisateur accède aux projets dont il est contributeur.
     */
    public function testUserCanList(): void
    {
        $client = static::createClient();
        $client->loginUser($this->createUser());
        $client->request('GET', '/project/');
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    /**
     * Vérifie qu'un utilisateur ni administrateur ni propriétaire ni contributeur ne peut voir le projet.
     */
    public function testUserCannotShow(): void
    {
        $client = static::createClient();
        $client->loginUser($this->createUser());
        $project = $this->createProject();
        $client->request('GET', "/project/{$project->getId()}");
        $this->assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    /**
     * Vérifie qu'un administrateur peut voir le projet.
     */
    public function testAdminCanShow(): void
    {
        $client = static::createClient();
        $project = $this->createProject();
        $client->loginUser($this->createAdmin());
        $client->request('GET', "/project/{$project->getId()}");
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    /**
     * Vérifie qu'un propriétaire peut voir le projet.
     */
    public function testOwerCanShow(): void
    {
        $client = static::createClient();
        $project = $this->createProject();
        $client->loginUser($project->getOwner());
        $client->request('GET', "/project/{$project->getId()}");
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    /**
     * Vérifie qu'un contributeur peut voir le projet.
     */
    public function testContributorCanShow(): void
    {
        $client = static::createClient();
        $project = $this->createProject();
        $client->loginUser($project->getContributors()->first());
        $client->request('GET', "/project/{$project->getId()}");
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    /**
     * Vérifie qu'un utilisateur peut créer un nouveau projet.
     */
    public function testUserCanCreate(): void
    {
        $client = static::createClient();
        $user = $this->createUser();
        $client->loginUser($user);
        $client->request('GET', '/project/new');
        $name = Factory::create()->text(50);
        $client->submitForm('Enregistrer', [
            'project[name]' => $name,
            'project[description]' => Factory::create()->text(255),
        ]);
        $this->assertSame(Response::HTTP_SEE_OTHER, $client->getResponse()->getStatusCode());
        $this->assertSame(1, $this->getProjectRepository()->count());
        $this->assertSame($user->getId(), $this->getProjectRepository()->findOneByName($name)->getOwner()->getId());
    }

    /**
     * Vérifie qu'un utilisateur ni administrateur ni propriétaire ni contributeur ne peut modifier un projet.
     */
    public function testUserCannotEdit(): void
    {
        $client = static::createClient();
        $project = $this->createProject();
        $user = $this->createUser();
        $client->loginUser($user);

        $client->request('GET', "/project/{$project->getId()}/edit");
        $this->assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    /**
     * Vérifie qu'un contributeur ne peut modifier un projet.
     */
    public function testContributorCannotEdit(): void
    {
        $client = static::createClient();
        $project = $this->createProject();
        $user = $this->createUser();
        $project->addContributor($user);
        $client->loginUser($user);

        $client->request('GET', "/project/{$project->getId()}/edit");
        $this->assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    /**
     * Vérifie qu'un administrateur peut modifier un projet.
     */
    public function testAdminCanEdit(): void
    {
        $client = static::createClient();
        $project = $this->createProject();
        $admin = $this->createAdmin();
        $client->loginUser($admin);

        $client->request('GET', "/project/{$project->getId()}/edit");
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $newName = Factory::create()->text(50);
        $newDescription = Factory::create()->text(255);
        $client->submitForm('Enregistrer', [
            'project[name]' => $newName,
            'project[description]' => $newDescription,
        ]);
        $this->assertSame(Response::HTTP_SEE_OTHER, $client->getResponse()->getStatusCode());
        $this->assertSame($newName, $this->getProjectRepository()->findOneById($project->getId())->getName());
        $this->assertSame($newDescription, $this->getProjectRepository()->findOneById($project->getId())->getDescription());
    }

    /**
     * Vérifie qu'un utilisateur propriétaire peut modifier un projet.
     */
    public function testOwnerCanEdit(): void
    {
        $client = static::createClient();
        $project = $this->createProject();
        $client->loginUser($project->getOwner());

        $client->request('GET', "/project/{$project->getId()}/edit");
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $newName = Factory::create()->text(50);
        $newDescription = Factory::create()->text(255);
        $client->submitForm('Enregistrer', [
            'project[name]' => $newName,
            'project[description]' => $newDescription,
        ]);
        $this->assertSame(Response::HTTP_SEE_OTHER, $client->getResponse()->getStatusCode());
        $this->assertSame($newName, $this->getProjectRepository()->findOneById($project->getId())->getName());
        $this->assertSame($newDescription, $this->getProjectRepository()->findOneById($project->getId())->getDescription());
    }

    /**
     * Vérifie qu'un utilisateur ni administrateur ni propriétaire ni contributeur ne peut supprimer un projet.
     */
    public function testUserCannotDelete(): void
    {
        $client = static::createClient();
        $project = $this->createProject();
        $user = $this->createUser();
        $client->loginUser($user);

        $client->request('POST', "/project/{$project->getId()}/delete");
        $this->assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    /**
     * Vérifie qu'un contributeur ne peut supprimer un projet.
     */
    public function testContributorCannotDelete(): void
    {
        $client = static::createClient();
        $project = $this->createProject();
        $user = $this->createUser();
        $project->addContributor($user);
        $client->loginUser($user);

        $client->request('POST', "/project/{$project->getId()}/delete");
        $this->assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    /**
     * Vérifie qu'un administrateur peut supprimer un projet.
     */
    public function testAdminCanDelete(): void
    {
        $client = static::createClient();
        $project = $this->createProject();
        $admin = $this->createAdmin();
        $client->loginUser($admin);

        $client->request('POST', "/project/{$project->getId()}/delete");
        $this->assertSame(Response::HTTP_SEE_OTHER, $client->getResponse()->getStatusCode());
    }

    /**
     * Vérifie qu'un utilisateur propriétaire peut supprimer un projet.
     */
    public function testOwnerCanDelete(): void
    {
        $client = static::createClient();
        $project = $this->createProject();
        $client->loginUser($project->getOwner());

        $client->request('POST', "/project/{$project->getId()}/delete");
        $this->assertSame(Response::HTTP_SEE_OTHER, $client->getResponse()->getStatusCode());
    }

    /**
     * Vérifie qu'un utilisateur ni administrateur ni propriétaire ni contributeur ne peut supprimer un contributeur du projet.
     */
    public function testUserCannotDeleteContributor(): void
    {
        $client = static::createClient();
        $project = $this->createProject();
        $user = $this->createUser();
        $client->loginUser($user);

        $client->request('POST', "/project/{$project->getId()}/{$user->getId()}/delete-contributor");
        $this->assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    /**
     * Vérifie qu'un contributeur ne peut supprimer un contributeur du projet.
     */
    public function testContributorCannotDeleteContributor(): void
    {
        $client = static::createClient();
        $project = $this->createProject();
        $user = $this->createUser();
        $project->addContributor($user);
        $client->loginUser($user);

        $client->request('POST', "/project/{$project->getId()}/{$user->getId()}/delete-contributor");
        $this->assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    /**
     * Vérifie qu'un administrateur peut supprimer un contributeur du projet.
     */
    public function testAdminCanDeleteContributor(): void
    {
        $client = static::createClient();
        $project = $this->createProject();
        $admin = $this->createAdmin();
        $client->loginUser($admin);

        $client->request('POST', "/project/{$project->getId()}/{$admin->getId()}/delete-contributor");
        $this->assertSame(Response::HTTP_SEE_OTHER, $client->getResponse()->getStatusCode());
    }

    /**
     * Vérifie qu'un utilisateur propriétaire peut supprimer un contributeur du projet.
     */
    public function testOwnerCanDeleteContributor(): void
    {
        $client = static::createClient();
        $project = $this->createProject();
        $client->loginUser($project->getOwner());

        $client->request('POST', "/project/{$project->getId()}/{$project->getOwner()->getId()}/delete-contributor");
        $this->assertSame(Response::HTTP_SEE_OTHER, $client->getResponse()->getStatusCode());
    }
}
