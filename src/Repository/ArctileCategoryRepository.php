<?php

namespace App\Repository;

use App\Entity\ArctileCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ArctileCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method ArctileCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method ArctileCategory[]    findAll()
 * @method ArctileCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArctileCategoryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ArctileCategory::class);
    }

//    /**
//     * @return ArctileCategory[] Returns an array of ArctileCategory objects
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
    public function findOneBySomeField($value): ?ArctileCategory
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
