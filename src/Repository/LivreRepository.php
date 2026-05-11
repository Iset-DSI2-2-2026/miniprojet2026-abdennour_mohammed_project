<?php

namespace App\Repository;

use App\Entity\Genre;
use App\Entity\Livre;
use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Livre>
 */
class LivreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Livre::class);
    }

    public function createFilteredQueryBuilder(?string $titre, ?Genre $genre, ?bool $disponible, ?Tag $tag): QueryBuilder
    {
        $qb = $this->createQueryBuilder('l')
            ->leftJoin('l.auteur', 'a')->addSelect('a')
            ->leftJoin('l.genre', 'g')->addSelect('g');

        if ($tag) {
            $qb->innerJoin('l.tags', 'tags')->addSelect('tags')
                ->andWhere('tags = :tag')
                ->setParameter('tag', $tag);
        } else {
            $qb->leftJoin('l.tags', 'tags')->addSelect('tags');
        }

        if ($titre) {
            $qb->andWhere('LOWER(l.titre) LIKE :titre')
                ->setParameter('titre', '%'.mb_strtolower($titre).'%');
        }
        if ($genre) {
            $qb->andWhere('l.genre = :genre')
                ->setParameter('genre', $genre);
        }
        if (null !== $disponible) {
            $qb->andWhere('l.disponible = :dispo')
                ->setParameter('dispo', $disponible);
        }

        return $qb->orderBy('l.datePublication', 'DESC');
    }

    /**
     * @return list<Livre>
     */
    public function findByFilters(?string $titre, ?Genre $genre, ?bool $disponible, ?Tag $tag): array
    {
        return $this->createFilteredQueryBuilder($titre, $genre, $disponible, $tag)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return list<Livre>
     */
    public function findLastAdded(int $limit = 5): array
    {
        return $this->createQueryBuilder('l')
            ->orderBy('l.id', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param array<int> $ids
     *
     * @return list<Livre>
     */
    public function findByIdsOrdered(array $ids): array
    {
        if ([] === $ids) {
            return [];
        }

        $qb = $this->createQueryBuilder('l');
        $qb->where($qb->expr()->in('l.id', ':ids'))
            ->setParameter('ids', $ids)
            ->orderBy('l.titre', 'ASC');

        return $qb->getQuery()->getResult();
    }
}
