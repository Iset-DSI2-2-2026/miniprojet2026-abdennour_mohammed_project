<?php

namespace App\Service;

use App\Repository\LivreRepository;

class BibliothequeStats
{
    public function __construct(private LivreRepository $livreRepository)
    {
    }

    public function getTotalLivres(): int
    {
        return (int) $this->livreRepository->count([]);
    }

    public function getLivresDisponibles(): int
    {
        return (int) $this->livreRepository->count(['disponible' => true]);
    }

    /**
     * @return array<string, int> genre name => count
     */
    public function getLivresParGenre(): array
    {
        $qb = $this->livreRepository->createQueryBuilder('l')
            ->select('g.nom AS nom', 'COUNT(l.id) AS cnt')
            ->join('l.genre', 'g')
            ->groupBy('g.id')
            ->orderBy('g.nom', 'ASC');

        $rows = $qb->getQuery()->getArrayResult();
        $out = [];
        foreach ($rows as $row) {
            $out[(string) $row['nom']] = (int) $row['cnt'];
        }

        return $out;
    }

    public function getTempsLectureTotal(): float
    {
        $qb = $this->livreRepository->createQueryBuilder('l')
            ->select('COALESCE(SUM(l.nbPages), 0)');

        $pages = (int) $qb->getQuery()->getSingleScalarResult();

        return $pages / 30.0;
    }
}
