<?php

namespace App\Repository;

use App\Entity\Track;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Track>
 */
class TrackRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Track::class);
    }

    /**
     * Persist and optionally flush a track entity
     */
    public function save(Track $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Remove and optionally flush a track entity
     */
    public function remove(Track $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Find tracks by artist name
     *
     * @return Track[]
     */
    public function findByArtist(string $artist): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.artist = :artist')
            ->setParameter('artist', $artist)
            ->orderBy('t.title', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Search tracks by title or artist (case-insensitive)
     *
     * @return Track[]
     */
    public function searchByTitleOrArtist(string $searchTerm): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('LOWER(t.title) LIKE LOWER(:search) OR LOWER(t.artist) LIKE LOWER(:search)')
            ->setParameter('search', '%' . $searchTerm . '%')
            ->orderBy('t.artist', 'ASC')
            ->addOrderBy('t.title', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find track by ISRC code
     */
    public function findByIsrc(string $isrc): ?Track
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.isrc = :isrc')
            ->setParameter('isrc', $isrc)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Get all tracks ordered by artist and title
     *
     * @return Track[]
     */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('t')
            ->orderBy('t.artist', 'ASC')
            ->addOrderBy('t.title', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get tracks with duration greater than specified seconds
     *
     * @return Track[]
     */
    public function findByMinDuration(int $minDuration): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.duration >= :minDuration')
            ->setParameter('minDuration', $minDuration)
            ->orderBy('t.duration', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Count total tracks
     */
    public function countAll(): int
    {
        return (int) $this->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Get total duration of all tracks in seconds
     */
    public function getTotalDuration(): int
    {
        $result = $this->createQueryBuilder('t')
            ->select('SUM(t.duration)')
            ->getQuery()
            ->getSingleScalarResult();

        return (int) ($result ?? 0);
    }
}