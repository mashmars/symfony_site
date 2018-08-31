<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Post::class);
    }

//    /**
//     * @return Post[] Returns an array of Post objects
//     */
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
    public function findOneBySomeField($value): ?Post
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    /**
     * 找出最热的博客
     */
    public function findHotPostByViews($max)
    {
        return  $this->createQueryBuilder('u')
                ->where('u.status = 1')                
                ->orderBy('u.views','desc')
                ->addOrderBy('u.id','desc')
                ->setMaxResults($max)
                ->getQuery()
                ->getResult();
    }
    /**
     * 查找属于该标签下的文章 因为要分页，所以只构造查询
     */
    public function createQueryByTag($value,$userid)
    {
        return $this->getEntityManager()
                    ->createQuery(
                        'SELECT p FROM App:Post p 
                        LEFT JOIN p.tags t
                        WHERE p.userid = :userid and t.id = :id and p.status=1
                        '
                    )->setParameter('userid',$userid)->setParameter('id',$value)
        ;
                    
    }
    /**
     * 查找该文章下的评论，分页
     */
    public function createQueryComment($value)
    {
        return $this->getEntityManager()
                ->createQuery(
                    'select p,t from App:Post p
                    left join p.comments t
                    where p.id = :id
                    '
                )->setParameter('id',$value)
        ;
    }
}
