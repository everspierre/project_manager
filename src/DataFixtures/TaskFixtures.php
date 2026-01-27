<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Repository\ProjectRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class TaskFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private ProjectRepository $projectRepository)
    {
    }

    /**
     * Création de 20 projets aléatoires.
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr-FR');
        $projects = $this->projectRepository->findAll();

        foreach ($projects as $project) {
            $nbTasks = $faker->numberBetween(1, 5);
            for ($j = 0; $j < $nbTasks; ++$j) {
                $task = new Task();
                $task->setName($faker->sentence());
                $task->setDescription($faker->text());
                $task->setStartingDate($faker->dateTimeBetween('-3 years', 'now'));
                $task->setEndingDate($faker->dateTimeBetween($task->getStartingDate(), '+3 months'));
                $task->setProject($project);
                $manager->persist($task);
            }
        }

        $manager->flush();
    }

    /**
     * Initialisation des dépendances en amont.
     */
    public function getDependencies(): array
    {
        return [
            ProjectFixtures::class,
        ];
    }
}
