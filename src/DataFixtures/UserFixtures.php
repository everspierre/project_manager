<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    /**
     * @param UserPasswordHasherInterface $passwordHasher
     */
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    /**
     * Création de 20 utilisateurs aléatoires.
     *
     * @param ObjectManager $manager
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr-FR');

        /**
         * Administrateur
         */
        $admin = new User();
        $admin->setFirstname('Admin');
        $admin->setLastname('Admin');
        $admin->setEmail('admin@project.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $password = $this->passwordHasher->hashPassword($admin, 'admin');
        $admin->setPassword($password);
        $manager->persist($admin);

        /**
         * Utilisateur
         */
        $user = new User();
        $user->setFirstname('User');
        $user->setLastname('User');
        $user->setEmail('user@project.com');
        $user->setRoles(['ROLE_USER']);
        $password = $this->passwordHasher->hashPassword($user, 'user');
        $user->setPassword($password);
        $manager->persist($user);

        for ($i=0; $i <= 20; $i++) {
            $user = new User();
            $user->setFirstname($faker->firstName);
            $user->setLastname($faker->lastName);
            $user->setEmail($faker->email);
            $user->setRoles([$faker->randomElement(['ROLE_USER', 'ROLE_ADMIN'])]);
            $password = $this->passwordHasher->hashPassword($user, $user->getFirstname());
            $user->setPassword($password);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
