<?php

namespace App\DataFixtures;

use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TagFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $items = [
            ['Bestseller', '#F39C12'],
            ['Classique', '#8E44AD'],
            ['Coup de cœur', '#E91E63'],
            ['Nouveau', '#16A085'],
            ['Jeunesse', '#2980B9'],
            ['Essai', '#7F8C8D'],
            ['Prix littéraire', '#C0392B'],
            ['Série', '#27AE60'],
        ];

        foreach ($items as $i => [$nom, $couleur]) {
            $t = new Tag();
            $t->setNom($nom);
            $t->setCouleur($couleur);
            $manager->persist($t);
            $this->addReference('tag_'.$i, $t);
        }

        $manager->flush();
    }
}
