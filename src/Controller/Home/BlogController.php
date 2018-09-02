<?php
namespace App\Controller\Home;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Home\BaseController;

use App\Entity\Post;
use App\Entity\Category;
use App\Entity\Tag;
use App\Entity\Comment;
use App\Entity\User;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/",  host="{subdomain}.site.com",defaults={"subdomain":"dev"})
 */
class BlogController extends BaseController
{
    private $limit = 3;
    private $categorys ; //分类
    private $tags; //标签
    private $post_hot ; //热门博客文章
    /**
     * 为什么没用__construct，因为在这个里面无法使用this->getDoctrine()->getManager(); TODO:
     */
    /**
     * 右侧信息，也可以用模板文档里直接引入控制器的内容 方法是 render(controller(App\\Controller\\Home\\BlogController::action)),
     * 需要在上面的方法里引入模板，传递相关参数
     * 这儿我使用的是直接传递，定义变量 因为这个时候的模板还需要再修改
     */
    public function common(Request $request)
    {         
        //获取当前是否已经是二级域名，如果是的话，访问博客一直是这个域名下面的内容，则需要考虑如何返回自己的域名
        //重写当前二级域名以及当前被浏览的用户
        $httpHost = $request->getHttpHost();
        $hosts = explode('.',$httpHost);
        if(count($hosts) === 2){
            $subdomain = 'mash';//默认的
        }elseif(count($hosts) === 3){
            $subdomain = $hosts[0];
        }else{
            throw $this->createNotFoundException('请求有误');
        }
        $session = $request->getSession(); //就不用通过SessionInterface $session 注入了
        $session->set('subdomain',$subdomain);
        //更新当前浏览的用户userid
        $repository_user = $this->getDoctrine()->getManager()->getRepository(User::class);
        $current_user = $repository_user->findOneBy(['domain'=>$subdomain]);
        $this->userinfo['userid'] = $current_user->getId();
        



        //分类 用户id传递也可以直接传递 $this->user  or $this->getUser()
        $respository_category = $this->getDoctrine()->getManager()->getRepository(Category::class);
        $categorys = $respository_category->findAllByUser($this->userinfo['userid']);
        //标签
        $respository_tag = $this->getDoctrine()->getManager()->getRepository(Tag::class);
        $tags = $respository_tag->findAllByUser($this->userinfo['userid']);        
        //最热博客文章
        $respository_post = $this->getDoctrine()->getManager()->getRepository(Post::class);
        $post_hot = $respository_post->findHotPostByViews(10);
        $this->categorys = $categorys;
        $this->tags = $tags;
        $this->post_hot = $post_hot;
    }
    
    /**
     * @Route("/blog-post-{page<\d+>?1}-list" , name="blog_index")
     */
    public function blog_post($page,Request $request)
    {               
        $this->common($request);
        $queryBuilder = $this->getDoctrine()->getManager()->getRepository(Post::class)->createQueryBuilder('u')
                        ->where('u.userid = :userid')->andWhere('u.status=1')->setParameter('userid',$this->userinfo['userid']);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($queryBuilder,$page,$this->limit);
        return $this->render("home/blog/blog_post_list.html.twig",[
            'pagination'=>$pagination,
            'categorys' => $this->categorys,
            'tags'  => $this->tags,
            'post_hot' => $this->post_hot,
        ]);
    }

    /**
     * @Route("/blog-post-category-{name}-{id<\d+>}-{page<\d+>?1}-list",name="blog_category")
     * blog分类
     */
    public function blog_post_by_category($name,$id,$page,Request $request)
    {
        $this->common($request);
        $queryBuilder = $this->getDoctrine()->getManager()->getRepository(Post::class)->createQueryBuilder('u')
                        ->where('u.category = :id')->andWhere('u.userid = :userid')->andWhere('u.status=1')
                        ->setParameter('id',$id)->setParameter('userid',$this->userinfo['userid']);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($queryBuilder,$page,$this->limit);
        return $this->render('home/blog/blog_post_list.html.twig',[
            'pagination' => $pagination,
            'categorys' => $this->categorys,
            'tags'  => $this->tags,
            'post_hot' => $this->post_hot,
        ]);
    }
    /**
     * @Route("/blog-post-tag-{name}-{id<\d+>}-{page<\d+>?1}-list",name="blog_tag")
     */
    public function blog_post_by_tag($name,$id,$page,Request $request)
    {
        $this->common($request);
        /* $queryBuilder = $this->getDoctrine()->getManager()->getRepository(Post::class)->createQueryBuilder('u')
                        ->where('u.tag = :id')->andWhere('u.userid = :userid')
                        ->setParameter('id',$id)->setParameter('userid',$this->userinfo['userid']); */
        $repository = $this->getDoctrine()->getManager()->getRepository(Post::class);
        $queryBuilder = $repository->createQueryByTag($id,$this->userinfo['userid']);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($queryBuilder,$page,$this->limit);
        return $this->render('home/blog/blog_post_list.html.twig',[
            'pagination' => $pagination,
            'categorys' => $this->categorys,
            'tags'  => $this->tags,
            'post_hot' => $this->post_hot,
        ]);
    }

    /**
     * @Route("/blog-post-detail-{title}-{id<\d+>}-{page<\d+>?1}-show",name="blog_detail")
     */
    public function blog_post_detail($title,$id,$page,Request $request)
    {
        
        $this->common($request);
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(Post::class);
        $post = $repository->find($id);
        if(!$post || $post->getStatus() != 1){
            throw $this->createNotFoundException('没有相关博客文章');
        }
        //获取相关评论
        // $queryBuilder = $repository->createQueryComment($id);
        $repository_comment = $this->getDoctrine()->getManager()->getRepository(Comment::class);
        $queryBuilder = $repository_comment->createQueryBuilder('u')->where('u.post = :id')->setParameter('id',$id);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($queryBuilder,$page,$this->limit);
        //阅读数++++
        $clientIp = $request->getClientIp();
        $redis = $this->get('redis');
        $is_add = $redis->get('post:'.$id . ':'.$clientIp);
        if(!$is_add){
            $post->setViews($post->getViews()+1);
            $entityManager->flush();
            $redis->setex('post:'.$id.':'.$clientIp,3600,1);//一个小时过期 setex(key,ttl,value)
        }
        
        return $this->render('home/blog/blog_post_detail.html.twig',[
            'pagination'=>$pagination,
            'post' => $post,
            'categorys' => $this->categorys,
            'tags'  => $this->tags,
            'post_hot' => $this->post_hot,
        ]);
    }

    /**
     * @Route("/blog-comment-delete",name="blog_comment_delete",methods={"POST"})
     */
    public function blog_comment_delete(Request $request)
    {
        if(!$this->user){
            return $this->json(['code'=>1,'msg'=>'请先登录系统']);
        }
        $id = $request->request->get('id');
        $id=5;
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(Comment::class);
        $comment = $repository->find($id);
        if(!$comment || $comment->getPost()->getUserid()->getId() != $this->user->getId())
        {
            return $this->json(['code'=>1,'msg'=>'没有找到该评论或非法请求']);
        }
        $entityManager->remove($comment);
        $entityManager->flush();
        
        return  $this->json(['code'=>0,'msg'=>'评论删除成功']);
    }

    /**
     * @Route("/blog-comment",name="blog_comment",methods={"POST","GET"})
     */
    public function blog_comment(Request $request)
    {
        $post_id = $request->request->get('post_id');
        $content = $request->request->get('content');
        $csrf_token = $request->request->get('csrf_token');
        if(!$this->user){
            return $this->json(['code'=>-1,'msg'=>'请先登陆']);
        }
        if(!$this->isCsrfTokenValid('comment',$csrf_token)){
            return $this->json(['code'=>2,'msg'=>'非法请求']);
        }
        if(!$post_id){
            return $this->json(['code'=>3,'msg'=>'请求有误']);
        }
        $entityManager =  $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(Post::class);
        $post = $repository->find($post_id);
        if(!$post || $post->getStatus() != 1){
            return $this->json(['code'=>1,'msg'=>'请求有误1']);
        }
        if($post->getIsComment() != 1){
            return $this->json(['code'=>1,'msg'=>'该博客文章没有开通评论权限']);
        }
        //可以评论
        $comment = new Comment();
        $comment->setPost($post);
        $comment->setUserid($this->user);
        $comment->setContent($content);
        $entityManager->persist($comment);
        $entityManager->flush();
        $this->addFlash('success','评论成功');
        return $this->json(['code'=>0,'msg'=>'评论成功']);
    }
}