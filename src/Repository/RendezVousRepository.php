<?php

namespace App\Repository;

use App\Entity\RendezVous;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RendezVous|null find($id, $lockMode = null, $lockVersion = null)
 * @method RendezVous|null findOneBy(array $criteria, array $orderBy = null)
 * @method RendezVous[]    findAll()
 * @method RendezVous[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RendezVousRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RendezVous::class);
    }


    // /**
    //  * @return RendezVous[] Returns an array of RendezVous objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */


    public function findByDate($date)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.date = :val')
               ->setParameter('val',$date)
            ->getQuery()
            ->getResult()
        ;
    }
    public function mesrdv($user_id)
    {

        $rawSQL ="SELECT rendez_vous.id as rdvid, rendez_vous.date, rendez_vous.service,horaire.* 
FROM rendez_vous INNER join horaire on rendez_vous.horaire_id = horaire.id
WHERE rendez_vous.user_id ='$user_id'ORDER BY rendez_vous.id DESC";
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


    public function toutRdv()
    {

        $rawSQL ="SELECT rendez_vous.id as rdv_id, rendez_vous.service, rendez_vous.date,
       user.*,horaire.* FROM rendez_vous INNER join user on rendez_vous.user_id = user.id 
           INNER join horaire on rendez_vous.horaire_id = horaire.id 
    WHERE rendez_vous.date > now() - INTERVAL 30 day ORDER BY rendez_vous.id DESC";
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

}
