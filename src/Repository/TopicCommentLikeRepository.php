<?php

namespace App\Repository;

use App\Entity\TopicCommentLike;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method TopicCommentLike|null find($id, $lockMode = null, $lockVersion = null)
 * @method TopicCommentLike|null findOneBy(array $criteria, array $orderBy = null)
 * @method TopicCommentLike[]    findAll()
 * @method TopicCommentLike[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TopicCommentLikeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TopicCommentLike::class);
    }

//    /**
//     * @return TopicCommentLike[] Returns an array of TopicCommentLike objects
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
    public function findOneBySomeField($value): ?TopicCommentLike
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
