<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail('admin@bookshelf.com');
        $admin->setPseudo('admin');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
        $manager->persist($admin);
        $this->addReference('user_admin', $admin);

        $biblio = new User();
        $biblio->setEmail('biblio@bookshelf.com');
        $biblio->setPseudo('biblio');
        $biblio->setRoles(['ROLE_BIBLIOTHECAIRE']);
        $biblio->setPassword($this->passwordHasher->hashPassword($biblio, 'biblio123'));
        $manager->persist($biblio);
        $this->addReference('user_biblio', $biblio);

        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < 5; ++$i) {
            $u = new User();
            $u->setEmail($faker->unique()->safeEmail());
            $u->setPseudo($faker->userName());
            $u->setRoles(['ROLE_USER']);
            $u->setPassword($this->passwordHasher->hashPassword($u, 'user12345'));
            $manager->persist($u);
            $this->addReference('user_'.$i, $u);
        }

        $manager->flush();
    }
}
