<?php

namespace App\DataFixtures;

use App\Entity\Genre;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GenreFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $items = [
            ['Roman', 'Romans contemporains et classiques.', '#E74C3C'],
            ['Science-fiction', 'Univers futuristes et technologies.', '#3498DB'],
            ['Policier', 'Enquêtes et suspense.', '#2C3E50'],
            ['Fantasy', 'Mondes imaginaires et magie.', '#9B59B6'],
            ['Biographie', 'Récits de vie.', '#1ABC9C'],
            ['Histoire', 'Faits historiques et essais.', '#D35400'],
        ];

        foreach ($items as $i => [$nom, $desc, $couleur]) {
            $g = new Genre();
            $g->setNom($nom);
            $g->setDescription($desc);
            $g->setCouleur($couleur);
            $manager->persist($g);
            $this->addReference('genre_'.$i, $g);
        }

        $manager->flush();
    }
}
