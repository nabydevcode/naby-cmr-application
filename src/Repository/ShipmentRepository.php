<?php

namespace App\Repository;

use App\Entity\Shipment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Shipment>
 */
class ShipmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Shipment::class);
    }

    public function searchByDateInterval(?\DateTimeInterface $start, ?\DateTimeInterface $end): array
    {
        $qb = $this->createQueryBuilder('p');

        if ($start) {
            $qb->andWhere('p.createdAt >= :start')
                ->setParameter('start', $start);
        }

        if ($end) {
            $qb->andWhere('p.createdAt <= :end')
                ->setParameter('end', $end);
        }

        return $qb->orderBy('p.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }


    //    /**
    //     * @return Shipment[] Returns an array of Shipment objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Shipment
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

}
