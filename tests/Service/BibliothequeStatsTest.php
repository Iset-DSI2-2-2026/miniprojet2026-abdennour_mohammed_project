<?php

namespace App\Tests\Service;

use App\Repository\LivreRepository;
use App\Service\BibliothequeStats;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;

class BibliothequeStatsTest extends TestCase
{
    public function testGetTotalLivres(): void
    {
        $repo = $this->createMock(LivreRepository::class);
        $repo->method('count')->with([])->willReturn(12);

        $stats = new BibliothequeStats($repo);

        self::assertSame(12, $stats->getTotalLivres());
    }

    public function testGetLivresDisponibles(): void
    {
        $repo = $this->createMock(LivreRepository::class);
        $repo->method('count')->with(['disponible' => true])->willReturn(8);

        $stats = new BibliothequeStats($repo);

        self::assertSame(8, $stats->getLivresDisponibles());
    }

    public function testGetTempsLectureTotal(): void
    {
        $repo = $this->createMock(LivreRepository::class);
        $qb = $this->createMock(QueryBuilder::class);
        $query = $this->createMock(Query::class);

        $repo->method('createQueryBuilder')->with('l')->willReturn($qb);
        $qb->method('select')->willReturnSelf();
        $qb->method('getQuery')->willReturn($query);
        $query->method('getSingleScalarResult')->willReturn(90);

        $stats = new BibliothequeStats($repo);

        self::assertEqualsWithDelta(3.0, $stats->getTempsLectureTotal(), 0.0001);
    }

    public function testGetLivresParGenre(): void
    {
        $repo = $this->createMock(LivreRepository::class);
        $qb = $this->createMock(QueryBuilder::class);
        $query = $this->createMock(Query::class);

        $repo->method('createQueryBuilder')->with('l')->willReturn($qb);
        $qb->method('select')->willReturnSelf();
        $qb->method('join')->willReturnSelf();
        $qb->method('groupBy')->willReturnSelf();
        $qb->method('orderBy')->willReturnSelf();
        $qb->method('getQuery')->willReturn($query);
        $query->method('getArrayResult')->willReturn([
            ['nom' => 'Roman', 'cnt' => '3'],
            ['nom' => 'SF', 'cnt' => 2],
        ]);

        $stats = new BibliothequeStats($repo);

        self::assertSame(['Roman' => 3, 'SF' => 2], $stats->getLivresParGenre());
    }
}
