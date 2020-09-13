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

    public function findFilteredSorties($form, $current_user)
    {
        $qb = $this->createQueryBuilder('s');

        if ($form->get('campus')->getData() != null)
        {
            $qb->andWhere('s.campus = :val')
                ->setParameter('val', $form->get('campus')->getData());
        }
        if ($form->get('nom')->getData() != null)
        {
            $qb->andWhere('s.nom LIKE :val2')
                ->setParameter('val2', '%'.$form->get('nom')->getData().'%');
        }

        if ($form->get('date_debut')->getData() != null)
        {
            $qb->andWhere('s.date_debut > :val3')
                ->setParameter('val3', $form->get('date_debut')->getData());
        }
        if ($form->get('date_fin')->getData() != null)
        {
            $qb->andWhere('s.date_fin < :val4')
                ->setParameter('val4', $form->get('date_fin')->getData());
        }
        if (!$form->get('owned')->getData())
        {
            $qb->andWhere('s.organisateur != :val5')
                ->setParameter('val5', $current_user);
        }
        if (!$form->get('expired')->getData())
        {
            $qb->andWhere('s.date_debut > :val6')
                ->setParameter('val6', new \DateTime('now'));
        }
        if (!$form->get('subscribed')->getData())
        {
            $user_sorties = $this->findUserSorties($current_user);
            $qb->andWhere('s NOT IN (:val7)')
                ->setParameter('val7', $user_sorties);
            
        }
        if (!$form->get('notSubscribed')->getData())
        {
            $user_sorties = $this->findUserSorties($current_user);
            $qb->andWhere('s IN (:val8)')
                ->setParameter('val8', $user_sorties);
        }
        
        return $qb->getQuery()->getResult();
    }

    // select * from sorties where sorties.id in (select sorties_id from inscriptions left join user on user.id = inscriptions.user_id where user_id = 3)
    public function findUserSorties($user)
    {
        $sortiesQb = $this->getEntityManager()->createQueryBuilder('sorties');
        $inscriptionQb = $this->getEntityManager()->createQueryBuilder('inscriptions');
        $sortiesQb->select('s')
            ->from('App:Sorties', 's')
            ->where(
                $sortiesQb->expr()->in(
                    's.id',
                    $inscriptionQb->select('IDENTITY(i.sorties)')
                    ->from('App:Inscriptions', 'i')
                    ->where('i.user = :user_id')
                    ->getDQL()
                )
            )->setParameter('user_id', $user);
        return $sortiesQb->getQuery()->getResult();
    
    }
}
