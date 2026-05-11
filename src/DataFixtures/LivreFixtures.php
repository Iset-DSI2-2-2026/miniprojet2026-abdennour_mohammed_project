<?php

namespace App\DataFixtures;

use App\Entity\Auteur;
use App\Entity\Genre;
use App\Entity\Livre;
use App\Entity\Tag;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class LivreFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 30; ++$i) {
            $livre = new Livre();
            $livre->setTitre($faker->sentence(4));
            $livre->setResume($faker->paragraphs(4, true));
            $livre->setIsbn($faker->isbn13());
            $livre->setNbPages($faker->numberBetween(50, 900));
            $livre->setDatePublication($faker->dateTimeBetween('-40 years', 'now'));
            $livre->setDisponible($faker->boolean(80));

            $livre->setAuteur($this->getReference('auteur_'.$faker->numberBetween(0, 4), Auteur::class));
            $livre->setGenre($this->getReference('genre_'.$faker->numberBetween(0, 5), Genre::class));

            $userRef = match ($faker->numberBetween(0, 6)) {
                0 => 'user_admin',
                1 => 'user_biblio',
                default => 'user_'.$faker->numberBetween(0, 4),
            };
            $livre->setAjoutePar($this->getReference($userRef, User::class));

            $tagCount = $faker->numberBetween(1, 4);
            /** @var list<int|string> $picked */
            $picked = $faker->randomElements(range(0, 7), $tagCount);
            foreach ($picked as $ti) {
                $livre->addTag($this->getReference('tag_'.(int) $ti, Tag::class));
            }

            $manager->persist($livre);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            GenreFixtures::class,
            TagFixtures::class,
            AuteurFixtures::class,
            UserFixtures::class,
        ];
    }
}
