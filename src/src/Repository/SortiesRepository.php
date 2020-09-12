<?php

namespace App\Repository;

use App\Entity\Inscriptions;
use App\Entity\Sorties;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sorties|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sorties|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sorties[]    findAll()
 * @method Sorties[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortiesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sorties::class);
    }

    // /**
    //  * @return Sorties[] Returns an array of Sorties objects
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
    public function findOneBySomeField($value): ?Sorties
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findFilteredSorties($filters)
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('s')
            ->from('Sorties', 's');
        if ($filters->get('campus') != null)
        {

        }
    }

    // select * from sorties where sorties.id in (select sorties_id from inscriptions left join user on user.id = inscriptions.user_id where user_id = 3)
    public function findUserSorties($user)
    {
        $sub = $this->getEntityManager()->createQueryBuilder()
            ->select('i.id')
            ->from('App:Inscriptions', 'i')
            ->where('i.user = ?1')
            ->getDQL();
        $qb = $this->getEntityManager()->createQueryBuilder('s')
            ->select('s')
            ->from('App:Sorties', 's')
            ->andWhere($this->getEntityManager()->createQueryBuilder()->expr()->in('s.id',$sub))
            ->setParameter(1, $user->getId());
        return $qb->getQuery()->getResult();
    
    }
}
