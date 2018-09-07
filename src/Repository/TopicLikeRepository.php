<?php

namespace App\Repository;

use App\Entity\TopicLike;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method TopicLike|null find($id, $lockMode = null, $lockVersion = null)
 * @method TopicLike|null findOneBy(array $criteria, array $orderBy = null)
 * @method TopicLike[]    findAll()
 * @method TopicLike[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TopicLikeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TopicLike::class);
    }

//    /**
//     * @return TopicLike[] Returns an array of TopicLike objects
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
    public function findOneBySomeField($value): ?TopicLike
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
