<?php

namespace App\DataFixtures;

use App\Entity\Auteur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AuteurFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 5; ++$i) {
            $a = new Auteur();
            $a->setNom($faker->lastName());
            $a->setPrenom($faker->firstName());
            $a->setBiographie($faker->paragraphs(3, true));
            $a->setNationalite($faker->country());
            $manager->persist($a);
            $this->addReference('auteur_'.$i, $a);
        }

        $manager->flush();
    }
}
