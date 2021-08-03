<?php

namespace App\Repository;

use App\Entity\Horaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Horaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method Horaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method Horaire[]    findAll()
 * @method Horaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HoraireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Horaire::class);
    }

    // /**
    //  * @return Horaire[] Returns an array of Horaire objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    */

    public function getheureId($heure)
    {

        $rawSQL ="SELECT id FROM horaire WHERE horaire.heure='$heure'";
        $stmt = false;
        try {
            $stmt = $this->getEntityManager()->getConnection()->prepare($rawSQL);
        } catch (Exception $e) {
            return 'ERROR WHILE PREPARING REQUEST';
        }
        try {
            $stmt->execute();
        } catch (\Doctrine\DBAL\Driver\Exception $e) {
            return 'ERROR WHILE EXECUTING REQUEST';
        }

        return $stmt->fetchAll();
    }
    public function getFullHeures(){
        return $this->createQueryBuilder('horaire')
            ->where('horaire.heure LIKE :full')
            ->setParameter(':full', "%:00")
            ->getQuery()
            ->getResult();
    }


    /*
    public function findOneBySomeField($value): ?Horaire
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
