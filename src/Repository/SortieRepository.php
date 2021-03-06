<?php

namespace App\Repository;

use App\Entity\Sortie;
use App\Services\NameState;
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

    /**
     * SortieRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }


    /**
     * Méthode retournant les sorties triées selon les états souhaités : 'Créée', 'Ouvertes', 'Clôturées', 'En cours'.
     * @return int|mixed|string
     */
    public function findEventsBySevralStates()
    {
        $queryBuilder = $this->createQueryBuilder('s')
            ->join('s.state', 'e')->addSelect('e')
            ->leftJoin('s.participants', 'p')->addSelect('p');
        // on cherche les sorties étants Ouvertes, Cloturées, et En cours :
        $queryBuilder
            ->where('e.wording IN (:open, :end, :progress, :create)')
            ->setParameter('open',NameState::STATE_OPEN)
            ->setParameter('end',NameState::STATE_END_REGISTER)
            ->setParameter('progress',NameState::STATE_IN_PROGRESS)
            ->setParameter('create',NameState::STATE_CREATED);
        $query = $queryBuilder->getQuery();
        return $query->getResult();
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
     * @param $user - Le Participant connecté.
     * @param $searchForm - L'objet SearchForm constituant la recherche de l'utilisateur.
     * @return int|mixed|string - Une liste de sortie corespondant à la recherche.
     */
    public function getEventsListSorted($user, $searchForm)
    {
        // on valorise ou non la saisie utilisateur
        $search = ($searchForm->getSearchInputText() !== null) ? $searchForm->getSearchInputText() : null;

        // on créer la requete MySQL avec le QueryBuilder.
        $queryBuilder = $this->createQueryBuilder('s');
        $queryBuilder->join('s.state', 'e');
        $queryBuilder->addSelect('e');
        $queryBuilder->leftjoin('s.participants', 'p');
        $queryBuilder->addSelect('p');
        $queryBuilder->join('s.organizer', 'o');
        $queryBuilder->addSelect('o');
        // filtre avec le campus
        if ($searchForm->getCampus() !== null) {
            $queryBuilder->where('s.organizingSite = :campus');
            $queryBuilder->setParameter('campus', $searchForm->getCampus());
        }
        // filtre avec les dates
        if ($searchForm->getStartDate() !== null) {
            $queryBuilder->andWhere('s.startDate >= :startDate ');
            $queryBuilder->setParameter('startDate', $searchForm->getStartDate());
        }
        if ($searchForm->getEndDate() !== null) {
            $queryBuilder->andWhere('s.startDate <= :endDate ');
            $queryBuilder->setParameter('endDate', $searchForm->getEndDate());
        }
        // filtre avec la saisie (concaténation des '%' pour filtrer juste un terme en MySql)
        if ($search !== null) {
            $queryBuilder->andWhere('s.name LIKE :userInput');
            $queryBuilder->setParameter('userInput', '%'.$search.'%');
        }
        // filtre avec les checkBoxes
        if ($searchForm->isItMeOrganizer()) {
            $queryBuilder->andWhere('s.organizer = :me');
            $queryBuilder->setParameter('me', $user);
        }
        if ($searchForm->isItMeRegister()) {
            // on vérifie par une requete s'il existe un utilisateur dans une selection
            // de participants à une sortie (liaison ManyToMany)
            $queryBuilder->andWhere(':me MEMBER OF s.participants');
            $queryBuilder->setParameter('me', $user);
        }
        if ($searchForm->isItMeNoRegister()) {
            // on vérifie par une requete si l'utilisateur n'existe pas dans une selection
            // de participants à une sortie (liaison ManyToMany)
            $queryBuilder->andWhere(':me NOT MEMBER OF s.participants');
            $queryBuilder->setParameter('me', $user);
        }
        if ($searchForm->isItEventsDone()) {
            $queryBuilder->andWhere('e.wording = :passee');
            $queryBuilder->setParameter('passee', NameState::STATE_DONE);
        }
        // filtre sur les sorties annulées et historisées
        $queryBuilder->andWhere('e.wording <> :annulee');
        $queryBuilder->setParameter('annulee', NameState::STATE_CANCELED);
        $queryBuilder->andWhere('e.wording <> :historisee');
        $queryBuilder->setParameter('historisee', NameState::STATE_HISTORIZED);

        // ordonnée par sa date
        $queryBuilder->orderBy('s.startDate', 'DESC');

        // on construit, soumet et récupère les résultats
        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }


}
