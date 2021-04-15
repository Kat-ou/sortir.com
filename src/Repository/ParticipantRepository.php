<?php

namespace App\Repository;

use App\Entity\Participant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Participant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Participant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Participant[]    findAll()
 * @method Participant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParticipantRepository extends ServiceEntityRepository implements UserLoaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Participant::class);
    }

    public function loadUserByUsername($usernameOrEmail)
    {
        return $this->createQueryBuilder('p')
            ->where('p.username = :query OR p.email = :query')
            ->setParameter('query', $usernameOrEmail)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof Participant) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * Méthode retournant si un utilisateur est déja inscrit en base de données selon son 'username' et son 'email' passés en paramètre.
     * @param string $username - Le pseudo du participant.
     * @param string $email - L'email du participant.
     * @return bool - True si une personne possède déjà se pseudo ou cette email ; sinon False.
     */
    public function isParticipantAlreadyExist(string $username, string $email): bool
    {
        $result = null;
        $areThereSevralResult = false;
        try {
            $queryBuilder = $this->createQueryBuilder('p');
            $queryBuilder
                ->where('p.username = :username')
                ->setParameter('username',$username)
                ->orWhere('p.email = :email')
                ->setParameter('email',$email);
            $query = $queryBuilder->getQuery();
            $result = $query->getOneOrNullResult();
        } catch (NonUniqueResultException $nure) {
            // gestion de la levée d'exception au cas ou, mais présence
            // de contraintes d'unicité sur les 2 champs intérrogés
            $areThereSevralResult = true;
        }
        return ($areThereSevralResult || $result != null);
    }

    // /**
    //  * @return Participant[] Returns an array of Participant objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Participant
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
