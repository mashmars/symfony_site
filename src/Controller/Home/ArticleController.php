<?php
namespace App\Controller\Home;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use App\Controller\Home\BaseController;

use App\Entity\Article;
class ArticleController extends BaseController
{
    /**
     * @Route("/article-category-{id<\d+>?0}-{page<\d+>?1}",name="article_index")
     */
    public function index($id,$page)
    {
        $this->common_base();
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(Article::class);
        //默认所有的文章
        if($id){
            $queryBuilder = $repository->createQueryBuilder('u')->where('u.category = :id')->andWhere('u.status=1')->setParameter('id',$id)->orderBy('u.id','desc');
        }else{
            $queryBuilder = $repository->createQueryBuilder('u')->where('u.status=1')->orderBy('u.id','desc');
        }
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($queryBuilder,$page,5);
        
        return $this->render('home/article/article_list.html.twig',[
            'article_category' => $this->article_category,
            'pagination'    => $pagination,
        ]);
    }

    /**
     * @Route("/article-{slug}-{id<\d+>}",name="article_detail")
     */
    public function article_detail($id)
    {
        $this->common_base();
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(Article::class);
        $article = $repository->find($id);
        if(!$article || $article->getStatus() == 0){
            throw $this->createNotFoundException('没有找到相关文章');
        }
        $prev = $repository->findOnePrevById($id);
        $next = $repository->findOneNextById($id);
        return $this->render('home/article/article_detail.html.twig',[
            'article_category'  => $this->article_category,
            'article'   => $article,
            'prev'  => $prev,
            'next'  => $next,
        ]);
    }
}