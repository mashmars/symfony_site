<?php
namespace App\Controller\Home;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Home\BaseController;

//图集分类
use App\Entity\Album;
use App\Entity\AlbumCategory;
//blog
use App\Entity\Post;
//article
use App\Entity\Article;

/**
 * @Route("/",host="dev.site.com")
 */
class IndexController extends BaseController
{
    
    /**
     * @Route("/",name="home")
     */
    public function index()
    {     
        $this->common_base();
        $entityManager = $this->getDoctrine()->getManager();
        $repository_album_category = $entityManager->getRepository(AlbumCategory::class);
        $repository_album = $entityManager->getRepository(Album::class);
        $repository_blog = $entityManager->getRepository(Post::class);
        $repository_article = $entityManager->getRepository(Article::class);
        //图集及相关6个文章
        $album_category = $repository_album_category->findAll();
        foreach($album_category as &$category)
        {
            $category->album = $repository_album->createQueryBuilder('u')->where("u.category=:category")
                                ->setParameter('category',$category)->setMaxResults(6)->getQuery()->getResult();
        }
        //最新的三个博客
        $blog = $repository_blog->createQueryBuilder('u')->orderBy('u.id','desc')->setMaxResults(3)->getQuery()->getResult();
        //最新的6个文章
        $article = $repository_article->createQueryBuilder('u')->orderBy('u.id','desc')->setMaxResults(6)->getQuery()->getResult();
        return $this->render('home/index.html.twig',[
            'article_category' => $this->article_category,
            'album_category'    =>  $album_category,
            'blog'  =>  $blog,
            'articles'=>$article,
        ]);
    }
}