<?php
namespace App\Controller\Home;

use App\Controller\Home\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\TopicCategory;
use App\Entity\Topic;
use App\Entity\TopicComment;
use App\Entity\TopicCommentLike;

//form
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class TopicController extends BaseController
{
    /**
     * @Route("/topic-name-{name?}-{page<\d+>?1}",name="topic_index")
     */
    public function index($name,$page)
    {
        $this->common_base();
        $limit = 10;
        $entityManager = $this->getDoctrine()->getManager();
        $repository_category = $entityManager->getRepository(TopicCategory::class);
        $repository = $entityManager->getRepository(Topic::class);
        if($name){
            $category = $repository_category->findOneBy(['name'=>$name]);
            if(!$category){
                throw $this->createNotFoundException('没有相关话题分类');
            }
            $queryBuilder = $repository->createQueryBuilder('u')->where('u.category = :id')->setParameter('id',$category->getId())->orderBy('u.id','desc');
        }else{
            $queryBuilder = $repository->createQueryBuilder('u')->orderBy('u.id','desc');
        }
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($queryBuilder,$page,$limit);

        //话题分类
        $categorys = $repository_category->findAll();
        //热门话题
        $topic_hot = $repository->createQueryBuilder('u')->orderBy('u.views','desc')->addOrderBy('u.id','desc')->setMaxResults(10)->getQuery()->getResult();
        return $this->render('home/topic/topic_post_list.html.twig',[
            'pagination'    =>  $pagination,
            'categorys'     =>  $categorys,
            'topic_hot'     =>  $topic_hot,
            'article_category' => $this->article_category,
        ]);
    }
    /**
     * @Route("/topic/new",name="topic_create")
     */
    public function create(Request $request)
    {
        $this->common_base();
        $entityManager = $this->getDoctrine()->getManager();
        $repository_category = $entityManager->getRepository(TopicCategory::class);
        $repository = $entityManager->getRepository(Topic::class);
        $topic = new Topic();
        //form
        $form = $this->createFormBuilder($topic)
            ->add('category',EntityType::class,[
                'label'=>'分类名称',
                'class'=>TopicCategory::class,
                'choice_label'=>'name',
                'query_builder'=>function(EntityRepository $er){
                    return $er->createQueryBuilder('u');
                }
            ])
            ->add('title',TextType::class,['label'=>'主题名称'])
            ->add('content',TextareaType::class,['label'=>'主题内容','attr'=>['rows'=>7]])
            ->add('submit',SubmitType::class,['label'=>'提交','attr'=>['class'=>'button primary hollow small']])
            ->getForm()
            ;
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $topic->setUserid($this->user);
            $entityManager->persist($topic);
            $entityManager->flush();
            return $this->redirectToRoute('topic_detail',['id'=>$topic->getID()]);
        }

        //话题分类
        $categorys = $repository_category->findAll();
        //热门话题
        $topic_hot = $repository->createQueryBuilder('u')->orderBy('u.views','desc')->addOrderBy('u.id','desc')->setMaxResults(10)->getQuery()->getResult();
        return $this->render('home/topic/topic_post_add.html.twig',[            
            'categorys'     =>  $categorys,
            'topic_hot'     =>  $topic_hot,
            'article_category' => $this->article_category,
            'form'          =>  $form->createView(),
        ]);
    }
    /**
     * @Route("/topic-show-{id<\d+>}-{page<\d+>?1}",name="topic_detail")
     */
    public function show($id,$page)
    {
        $limit = 10;
        $this->common_base();
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(Topic::class);
        $repository_comment = $entityManager->getRepository(TopicComment::class);
        $repository_category = $entityManager->getRepository(TopicCategory::class);
        $topic = $repository->find($id);
        if(!$topic){
            throw $this->createNotFoundException('没有找到相关主题');
        }
        $queryBuilder = $repository_comment->createQueryBuilder('u')
                        ->where('u.topic = :id')->setParameter('id',$id);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($queryBuilder,$page,$limit);
        //阅读数+1
        $redis = $this->get('redis');
        $is = $redis->get('topic:'.$id.':view');
        if(!$is){
            $topic->setViews($topic->getViews()+1);
            $entityManager->flush();
            $redis->setex('topic:'.$id.':view',3600,1);//一个小时过期 setex(key,ttl,value)
        }

        //话题分类
        $categorys = $repository_category->findAll();
        //热门话题
        $topic_hot = $repository->createQueryBuilder('u')->orderBy('u.views','desc')->addOrderBy('u.id','desc')->setMaxResults(10)->getQuery()->getResult();
        return $this->render('home/topic/topic_post_detail.html.twig',[
            'article_category'  =>  $this->article_category,
            'categorys' =>  $categorys,
            'topic_hot' =>  $topic_hot,
            'topic'     =>  $topic,
            'pagination'    =>  $pagination,
        ]);
    }
    /**
     * @Route("/topic-comment-add",name="topic_comment",methods={"POST"})
     * 评论
     */
    public function comment_add(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $content = $request->request->get('content');
        $csrf_token = $request->request->get('csrf_token');
        $topic_id = $request->request->get('topic_id');
        if(!$this->user){
            return $this->json(['code'=>-1,'msg'=>'请先登陆系统']);
        }
        if(!$this->isCsrfTokenValid('topic',$csrf_token)){
            return $this->json(['code'=>1,'msg'=>'非法请求']);
        }
        if(!$topic_id || empty($content))
        {
            return $this->json(['code'=>1,'msg'=>'请求参数不正确']);
        }
        $repository_topic = $entityManager->getRepository(Topic::class);
        $topic = $repository_topic->find($topic_id);
        if(!$topic){
            return $this->json(['code'=>1,'msg'=>'请求资源不存在']);
        }
        $comment = new TopicComment();
        $comment->setContent($content);
        $comment->setTopic($topic);
        $comment->setUserid($this->user);
        $entityManager->persist($comment);
        $entityManager->flush();
        //评论+1
        $topic->setCommentCount($topic->getCommentCount()+1);
        $entityManager->persist($topic);
        $entityManager->flush();
        return $this->json(['code'=>0,'msg'=>'评论成功']);
    }
    /**
     * @Route("/topic-comment-delete",name="topic_comment_delete",methods={"POST"})
     */
    public function comment_delete(Request $request)
    {
        $id = $request->request->get('id');
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(TopicComment::class);
        $comment = $repository->find($id);
        if(!$comment){
            return $this->json(['code'=>1,'msg'=>'请求资源不存在']);
        }
        $topic = $comment->getTopic();
        if(!$this->user){
            return $this->json(['code'=>-1,'msg'=>'请先登陆系统']);
        }
        if($this->user->getId() != $topic->getUserid()->getId())
        {
            return $this->json(['code'=>1,'msg'=>'非法请求']);
        }
        //
        $entityManager->remove($comment);
        $entityManager->flush();
        //评论-1
        $topic->setCommentCount($topic->getCommentCount()-1);
        $entityManager->persist($topic);
        $entityManager->flush();
        return $this->json(['code'=>0,'msg'=>'删除成功']);
    }
    /**
     * @Route("/topic-comment-like",name="topic_comment_like",methods={"POST"})
     * 点赞
     */
    public function comment_like(Request $request)
    {
        $id = $request->request->get('id');
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(TopicComment::class);
        $comment = $repository->find($id);
        if(!$this->user){
            return $this->json(['code'=>-1,'msg'=>'请先登陆系统']);
        }
        if(!$comment){
            return $this->json(['code'=>1,'msg'=>'请求资源不存在']);
        }        
        
        $repository_like = $entityManager->getRepository(TopicCommentLike::class);        
        //判断是否点过赞 type 1是like 2是unlink (unlike)
        $like = $repository_like->createQueryBuilder('u')
                ->where("u.comment = :comment")->andWhere("u.userid = :userid")->andWhere("u.type=1")
                ->setParameter("comment",$comment)->setParameter("userid",$this->user)->setMaxResults(1)
                ->getQuery()->getOneOrNullResult();
        if($like){
            return $this->json(['code'=>1,'msg'=>'已经点过赞']);
        }
        //
        $commentLike = new TopicCommentLike();
        $commentLike->setComment($comment);
        $commentLike->setUserid($this->user);
        $commentLike->setType(1);

        $entityManager->persist($commentLike);
        $entityManager->flush();
        //点赞+1
        $comment->setLikeCount($comment->getLikeCount()+1);
        $entityManager->persist($comment);
        $entityManager->flush();
        return $this->json(['code'=>0,'msg'=>'点赞成功']);
    }
    /**
     * @Route("/topic-comment-dislike",name="topic_comment_dislike",methods={"POST"})
     * 嘲讽
     */
    public function comment_dislike(Request $request)
    {
        $id = $request->request->get('id');
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(TopicComment::class);
        $comment = $repository->find($id);
        if(!$this->user){
            return $this->json(['code'=>-1,'msg'=>'请先登陆系统']);
        }
        if(!$comment){
            return $this->json(['code'=>1,'msg'=>'请求资源不存在']);
        }        
        
        $repository_like = $entityManager->getRepository(TopicCommentLike::class);        
        //判断是否点过赞 type 1是like 2是unlink (unlike)
        $like = $repository_like->createQueryBuilder('u')
                ->where("u.comment = :comment")->andWhere("u.userid = :userid")->andWhere("u.type=2")
                ->setParameter("comment",$comment)->setParameter("userid",$this->user)->setMaxResults(1)
                ->getQuery()->getOneOrNullResult();
        if($like){
            return $this->json(['code'=>1,'msg'=>'已经嘲讽过']);
        }
        //
        $commentLike = new TopicCommentLike();
        $commentLike->setComment($comment);
        $commentLike->setUserid($this->user);
        $commentLike->setType(2);

        $entityManager->persist($commentLike);
        $entityManager->flush();
        //点赞+1
        $comment->setUnLinkCount($comment->getUnLinkCount()+1);
        $entityManager->persist($comment);
        $entityManager->flush();
        return $this->json(['code'=>0,'msg'=>'嘲讽成功']);
    }

}