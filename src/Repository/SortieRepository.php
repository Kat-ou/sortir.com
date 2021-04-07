<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }


    public function findAllElementsByEvent($id){
        $queryBuilder = $this->createQueryBuilder('s')
            ->leftJoin('s.participants','p')->addSelect('p')
            ->join('s.organizingSite','o')->addSelect('o')
            ->join('s.location','l')->addSelect('l')
            ->join('l.city','c')->addSelect('c');
        $queryBuilder
            ->andWhere('s.id = :id')->setParameter('id',$id);
        $query = $queryBuilder->getQuery();
        return $query->getSingleResult();
    }


    public function getEventsListSorted($campusId, $startDate, $endDate, $userInput)
    {
        $queryBuilder = $this->createQueryBuilder('s');
        $queryBuilder->join('s.state', 'e');
        $queryBuilder->join('s.participants', 'p');
        // filtre avec le campus
        $queryBuilder->where('s.organizingSite = :campusId');
        $queryBuilder->setParameter('campusId', $campusId);
        // filtre avec les dates
        $queryBuilder->andWhere('s.startDate >= :startDate ');
        $queryBuilder->setParameter('startDate', $startDate);
        //$queryBuilder->andWhere('s.startDate <= :endDate ');
        //$queryBuilder->setParameter('endDate', $endDate);
        // filtre avec la saisie
        //$queryBuilder->andWhere(
        //    $queryBuilder->expr()->like('s.name', ':userInput')
        //);
        //$queryBuilder->setParameter('userInput', $userInput);
        // filtre avec les checkBoxes

        $queryBuilder->addSelect('e');
        $queryBuilder->addSelect('p');
        $query = $queryBuilder->getQuery();
        return $query->execute();
    }


    // /**
    //  * @return Sortie[] Returns an array of Sortie objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Sortie
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
