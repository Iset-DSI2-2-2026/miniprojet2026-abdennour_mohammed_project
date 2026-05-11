<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

class LivreControllerTest extends WebTestCase
{
    public function testLivresPageIsSuccessful(): void
    {
        $client = static::createClient();
        $client->request('GET', '/livres');

        self::assertResponseIsSuccessful();
    }

    public function testLivresPageContainsTable(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/livres');

        self::assertGreaterThan(0, $crawler->filter('table')->count());
    }

    public function testNouveauLivreRedirectsWhenNotAuthenticated(): void
    {
        $client = static::createClient();
        $client->request('GET', '/livres/nouveau');

        self::assertResponseRedirects();
        self::assertTrue($client->getResponse()->isRedirect());
    }

    public function testCreateLivreShowsFlash(): void
    {
        $client = static::createClient();
        $client->loginUser(
            static::getContainer()->get('doctrine')->getRepository(\App\Entity\User::class)->findOneBy(['email' => 'biblio@bookshelf.com']),
        );

        $crawler = $client->request('GET', '/livres/nouveau');
        self::assertResponseIsSuccessful();

        $auteurId = null;
        $crawler->filter('select[name="livre[auteur]"] option')->each(function (Crawler $node) use (&$auteurId): void {
            $v = $node->attr('value');
            if ('' !== (string) $v && null === $auteurId) {
                $auteurId = $v;
            }
        });

        $genreId = null;
        $crawler->filter('select[name="livre[genre]"] option')->each(function (Crawler $node) use (&$genreId): void {
            $v = $node->attr('value');
            if ('' !== (string) $v && null === $genreId) {
                $genreId = $v;
            }
        });

        self::assertNotNull($auteurId);
        self::assertNotNull($genreId);

        $form = $crawler->selectButton('Enregistrer')->form([
            'livre[titre]' => 'Un titre de test assez long',
            'livre[resume]' => 'Un résumé suffisamment long pour satisfaire la contrainte minimale.',
            'livre[isbn]' => '9783158157451',
            'livre[nbPages]' => 120,
            'livre[datePublication]' => '2020-01-15',
            'livre[disponible]' => 1,
            'livre[auteur]' => $auteurId,
            'livre[genre]' => $genreId,
        ]);

        $client->submit($form);

        self::assertResponseRedirects('/livres');
        $client->followRedirect();

        self::assertSelectorTextContains('.alert-success', 'créé');
    }
}
