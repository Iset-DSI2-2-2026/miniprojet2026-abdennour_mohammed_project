<?php

namespace App\Tests\Api;

use App\Repository\AuteurRepository;
use App\Repository\GenreRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LivreApiTest extends WebTestCase
{
    public function testGetCollectionReturnsLdJson(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/livres', [], [], [
            'HTTP_ACCEPT' => 'application/ld+json',
        ]);

        self::assertResponseIsSuccessful();
        self::assertJson($client->getResponse()->getContent());
        $ct = (string) $client->getResponse()->headers->get('Content-Type');
        self::assertStringContainsString('application/ld+json', $ct);
    }

    public function testPostValidReturnsCreated(): void
    {
        $client = static::createClient();

        $auteur = static::getContainer()->get(AuteurRepository::class)->findOneBy([]);
        $genre = static::getContainer()->get(GenreRepository::class)->findOneBy([]);
        self::assertNotNull($auteur);
        self::assertNotNull($genre);

        $auteurIri = '/api/auteurs/'.$auteur->getId();
        $genreIri = '/api/genres/'.$genre->getId();

        $payload = [
            '@context' => '/api/contexts/Livre',
            '@type' => 'Livre',
            'titre' => 'Titre API valide assez long',
            'resume' => 'Un résumé suffisamment long pour passer la validation minimale requise par le projet.',
            'isbn' => '9788877331625',
            'nbPages' => 200,
            'datePublication' => '2018-05-01',
            'disponible' => true,
            'auteur' => $auteurIri,
            'genre' => $genreIri,
        ];

        $client->request(
            'POST',
            '/api/livres',
            [],
            [],
            ['CONTENT_TYPE' => 'application/ld+json', 'HTTP_ACCEPT' => 'application/ld+json'],
            json_encode($payload, JSON_THROW_ON_ERROR),
        );

        self::assertResponseStatusCodeSame(201);
    }

    public function testPostInvalidReturns422(): void
    {
        $client = static::createClient();

        $auteur = static::getContainer()->get(AuteurRepository::class)->findOneBy([]);
        $genre = static::getContainer()->get(GenreRepository::class)->findOneBy([]);
        self::assertNotNull($auteur);
        self::assertNotNull($genre);

        $auteurIri = '/api/auteurs/'.$auteur->getId();
        $genreIri = '/api/genres/'.$genre->getId();

        $payload = [
            '@context' => '/api/contexts/Livre',
            '@type' => 'Livre',
            'titre' => 'x',
            'resume' => 'court',
            'isbn' => '123',
            'nbPages' => 0,
            'datePublication' => '2018-05-01',
            'disponible' => true,
            'auteur' => $auteurIri,
            'genre' => $genreIri,
        ];

        $client->request(
            'POST',
            '/api/livres',
            [],
            [],
            ['CONTENT_TYPE' => 'application/ld+json', 'HTTP_ACCEPT' => 'application/ld+json'],
            json_encode($payload, JSON_THROW_ON_ERROR),
        );

        self::assertResponseStatusCodeSame(422);
    }
}
