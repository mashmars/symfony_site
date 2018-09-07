<?php

namespace App\Repository;

use App\Entity\AlbumCollect;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method AlbumCollect|null find($id, $lockMode = null, $lockVersion = null)
 * @method AlbumCollect|null findOneBy(array $criteria, array $orderBy = null)
 * @method AlbumCollect[]    findAll()
 * @method AlbumCollect[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AlbumCollectRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AlbumCollect::class);
    }

//    /**
//     * @return AlbumCollect[] Returns an array of AlbumCollect objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AlbumCollect
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
