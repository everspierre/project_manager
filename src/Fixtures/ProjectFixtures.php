<?php

namespace App\Fixtures;

use App\Entity\Project;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ProjectFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @param UserRepository $userRepository
     */
    public function __construct(private UserRepository $userRepository)
    {
    }

    /**
     * Création de 20 projets aléatoires.
     *
     * @param ObjectManager $manager
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr-FR');
        $users = $this->userRepository->findAll();

        for ($i=0; $i <= 40; $i++) {
            $project = new Project();
            $project->setName($faker->text(50));
            $project->setDescription($faker->text(200));
            $project->setOwner($faker->randomElement($users));
            $project->addContributor($project->getOwner());
            for ($j=0; $j < $faker->numberBetween(1, 5); $j++) {
                $project->addContributor($faker->randomElement($users));
            }
            $manager->persist($project);
        }

        $manager->flush();
    }

    /**
     * Initialisation des dépendances en amont.
     *
     * @return array
     */
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
