<?php

namespace App\Repository;

use App\Entity\People;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<People>
 */
class PeopleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, People::class);
    }

    //    /**
    //     * @return People[] Returns an array of People objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?People
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function countMealPreference($value): int
    {
        return $this->createQueryBuilder('p')
            ->select('count(p.id)')
            ->andWhere('p.mealPreference = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function countActivity($value): int
    {
        return $this->createQueryBuilder('p')
            ->select('count(p.id)')
            ->andWhere('p.activity = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
    

    public function countAccomodation($value): int
    {
        return $this->createQueryBuilder('p')
            ->select('count(p.id)')
            ->andWhere('p.location = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function countYoga(): int
    {
        return $this->createQueryBuilder('p')
            ->select('count(p.id)')
            ->andWhere('p.yoga = 1')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function getAttendency()
    {
        return $this->createQueryBuilder('p')
        ->select('count(p.id)')
        ->innerJoin('p.rsvp', 'r')
        ->andWhere('r.isAttending = 1')
        ->getQuery()
        ->getSingleScalarResult()
    ;
    }
}
