<?php
namespace App\Controller\Home;

use App\Controller\Home\BaseController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

//album图片专辑
use App\Entity\Album;
use App\Entity\AlbumCategory;
use App\Entity\AlbumComment;

class AlbumController extends BaseController
{
    protected $category;
    public function common()
    {
        $repository = $this->getDoctrine()->getManager()->getRepository(AlbumCategory::class);
        $category = $repository->findAll();
        $this->category = $category;
    }
    /**
     * @Route("/album-{name?0}-{userid<\d+>?0}-{page<\d+>?1}",name="album_index")
     */
    public function album_index($page,$name=null,$userid=null)
    {
        $this->common();
        $this->common_base();
        $limit = 6;
        $repository = $this->getDoctrine()->getManager()->getRepository(Album::class);
        $repository_category = $this->getDoctrine()->getManager()->getRepository(AlbumCategory::class);
        $category = $repository_category->findOneBy(['name'=>$name]);
        if($category && $userid){
            $queryBuilder = $repository->createQueryBuilder('u')                        
                        ->where('u.category=:category')->andWhere('u.useri:userid')->setParameter('userid',$userid)->setParameter('category',$category)->orderBy('u.id','desc')
                        ->getQuery()
            ;
        }elseif($category){
            $queryBuilder = $repository->createQueryBuilder('u')                        
                        ->where('u.category=:category')->setParameter('category',$category)->orderBy('u.id','desc')
                        ->getQuery()
            ;
        }if($userid){
            $queryBuilder = $repository->createQueryBuilder('u')                        
                        ->where('u.userid=:userid')->setParameter('userid',$userid)->orderBy('u.id','desc')
                        ->getQuery()
            ;
        }else{
            $queryBuilder = $repository->createQueryBuilder('u')                        
                        ->orderBy('u.id','desc')
                        ->getQuery()
            ;
        }        
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($queryBuilder,$page,$limit);

        
        return $this->render('home/album/album_index.html.twig',[
            'pagination'    =>  $pagination,
            'album_category'=>$this->category,
            'article_category' => $this->article_category,
        ]);
    }
    /**
     * @Route("/album-image-{id<\d+>}-show-{page<\d+>?1}",name="album_image_show")
     */
    public function album_show($id,$page)
    {
        $this->common();
        $this->common_base();
        $limit = 5;
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(Album::class);
        $album = $repository->find($id);
        if(!$album)
        {
            throw $this->createNotFoundException('没有找到相关信息');
        }
        //评论
        $repsoritory_album_comment = $this->getDoctrine()->getManager()->getRepository(AlbumComment::class);
        $queryBuilder = $repsoritory_album_comment->createQueryBuilder('u')
                        ->where("u.album = :album")->setParameter('album',$id)->getQuery();
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($queryBuilder,$page,$limit);
        //阅读数+1
        $redis = $this->get('redis');
        
        $is_comment = $redis->get('album:'.$this->user->getId() . ':comment');
        if(!$is_comment){
            $redis->setex('album:'.$this->user->getId() . ':comment',3600,1);
            $album->setViews($album->getViews()+1);
            $entityManager->persist($album);
            $entityManager->flush();
        }
        return $this->render('home/album/album_detail.html.twig',[
            'album' =>  $album,
            'pagination'    =>  $pagination,
            'album_category'=>$this->category,
            'article_category' => $this->article_category,
        ]);
    }
    /**
     * @Route("/album/comment",name="album_comment",methods={"POST"})
     */
    public function album_comment(Request $request)
    {
        $album_id = $request->request->get('album_id');
        $content = $request->request->get('content');
        $csrf_token = $request->request->get('csrf_token');
        if(!$this->user){
            return $this->json(['code'=>-1,'msg'=>'请先登录系统']);
        }
        if(!$this->isCsrfTokenValid('album-comment',$csrf_token))
        {
            return $this->json(['code'=>1,'msg'=>'非法请求']);
        }
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(Album::class);
        $album = $repository->find($album_id);
        if(!$album || $album->getUserid()->getId() != $this->user->getId()){
            return $this->json(['code'=>1,'msg'=>'请求信息不正确']);
        }
        //
        $comment = new AlbumComment();
        $comment->setUserid($this->user);
        $comment->setAlbum($album);
        $comment->setContent($content);
        $entityManager->persist($comment);
        $entityManager->flush();
        //评论+1
        $album->setCommentCount($album->getCommentCount() + 1);
        $entityManager->persist($album);
        $entityManager->flush();
        return $this->json(['code'=>0,'msg'=>'评论成功']);
    }
}