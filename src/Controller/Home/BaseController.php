<?php
namespace App\Controller\Home;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;


use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

//
use App\Entity\ArticleCategory;



class BaseController extends Controller
{
    protected $user; //登录的用户
    protected $userinfo = []; //个人相关信息

    protected $article_category ; //文章分类
   
    public function __construct(Security $security,SessionInterface $session)
    {       
        //获取当前是否已经是二级域名，如果是的话，访问博客一直是这个域名下面的内容，则需要考虑如何返回自己的域名
        //我去 构造函数里竟然无法获取host，只能放到blog里的公共方法里调用了TODO
        $this->user = $security->getUser(); //获取当前登陆用户       
        $subdomain = $session->get('subdomain');
        if(!$this->user){
            $userid = 2;
            $session->set('subdomain','dev'); //这儿设置subdomain是给其他地方用的           
        }else{
            $domain = $this->user->getDomain() ? $this->user->getDomain() : 'dev';
            $session->set('subdomain',$domain); //这儿设置subdomain是给其他地方用的
            $userid = $this->user->getId();            
            //
        }
        
        $this->userinfo['userid'] = $userid;
    }

    public function common_base()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository_article_category = $entityManager->getRepository(ArticleCategory::class);
        $this->article_category = $repository_article_category->findAll();
       
    }
    
}