<?php

namespace App\Repository;

use App\Entity\TopicCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method TopicCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method TopicCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method TopicCategory[]    findAll()
 * @method TopicCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TopicCategoryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TopicCategory::class);
    }

//    /**
//     * @return TopicCategory[] Returns an array of TopicCategory objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TopicCategory
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
