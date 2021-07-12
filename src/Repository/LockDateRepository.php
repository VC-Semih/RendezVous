<?php

namespace App\Repository;

use App\Entity\LockDate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LockDate|null find($id, $lockMode = null, $lockVersion = null)
 * @method LockDate|null findOneBy(array $criteria, array $orderBy = null)
 * @method LockDate[]    findAll()
 * @method LockDate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LockDateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LockDate::class);
    }

    // /**
    //  * @return LockDate[] Returns an array of LockDate objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?LockDate
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
