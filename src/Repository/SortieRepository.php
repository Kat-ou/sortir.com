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


    /**
     * Méthode permettant la requete de filtrage pour rechercher une liste de Sortie.
     * Les parametre sont constitués des éléments du formulaire de recherche en page d'accueil.
     * @param $user - Le Participant connecté
     * @param $campusId - Le campus sélectionné
     * @param $startDate - La date de début saisie
     * @param $endDate - La date de fin saisie
     * @param $userInput - Le texte saisie par le participant
     * @param $isItMeOrganizer - Le filtre si le participant est organisateur
     * @param $isItMeRegister - Le filtre si le participant est inscrit
     * @param $isItMeNoRegister -  Le filtre si le participant n'est pas inscrit
     * @param $isItEventsDone - Le filtre pour obtenir les sorties passées
     * @return int|mixed|string - Une liste de sortie corespondant à la recherche.
     */
    public function getEventsListSorted($user, $campusId, $startDate, $endDate, $userInput,
                                        $isItMeOrganizer, $isItMeRegister, $isItMeNoRegister,
                                        $isItEventsDone)
    {
        $queryBuilder = $this->createQueryBuilder('s');
        $queryBuilder->join('s.state', 'e');
        $queryBuilder->addSelect('e');
        $queryBuilder->join('s.participants', 'p');
        $queryBuilder->addSelect('p');
        $queryBuilder->join('s.organizer', 'o');
        $queryBuilder->addSelect('o');
        // filtre avec le campus
        if ($campusId !== null) {
            $queryBuilder->where('s.organizingSite = :campusId');
            $queryBuilder->setParameter('campusId', $campusId);
        }
        // filtre avec les dates
        if ($startDate !== null && $endDate !== null) {
            $queryBuilder->andWhere('s.startDate >= :startDate ');
            $queryBuilder->setParameter('startDate', $startDate);
            $queryBuilder->andWhere('s.startDate <= :endDate ');
            $queryBuilder->setParameter('endDate', $endDate);
        }
        // filtre avec la saisie (concaténation des '%' pour filtrer juste un terme en MySql)
        if ($userInput !== null) {
            $queryBuilder->andWhere('s.name LIKE :userInput');
            $queryBuilder->setParameter('userInput', '%'.$userInput.'%');
        }
        // filtre avec les checkBoxes
        if ($isItMeOrganizer === true) {
            $queryBuilder->andWhere('s.organizer = :me');
            $queryBuilder->setParameter('me', $user);
        }
        if ($isItMeRegister === true) {
            $queryBuilder->andWhere('s.participants = :me');
            $queryBuilder->setParameter('me', $user);
        }
        if ($isItMeNoRegister === true) {
            $queryBuilder->andWhere('s.participants <> :me');
            $queryBuilder->setParameter('me', $user);
        }
        if ($isItEventsDone === true) {
            $queryBuilder->andWhere('e.wording = :passee');
            $queryBuilder->setParameter('passee', 'Passée');
        }

        $query = $queryBuilder->getQuery();
        return $query->getResult();
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
