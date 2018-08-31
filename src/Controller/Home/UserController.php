<?php
namespace App\Controller\Home;
use App\Controller\Home\BaseController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
//个人资料
use App\Entity\User;
use App\Form\Home\UserProfileType;
//blog分类
use App\Entity\Category;
use App\Form\Home\CategoryAddType;
//blog标签
use App\Entity\Tag;
//blog文章
use App\Entity\Post;
use App\Form\Home\PostType;

/**
 * @Route("/",host="dev.site.com")
 */
class UserController extends BaseController
{
    
    /**
     * @Route("/user-index", name="user_index")
     */
    public function index(Request $request)
    {
        $user = $this->getUser();
        $form = $this->createForm(UserProfileType::class,$user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            $this->addFlash('success','修改个人信息成功');
            return $this->redirectToRoute('user_index');
        }
        return $this->render('home/user/index.html.twig',[
            'form'=>$form->createView(),
        ]);
    }


    /**
     * @Route("/user-blog-category",name="user_blog_category")
     */
    public function category_index()
    {
        $user = $this->getUser();
        $category = $user->getCategories();
        
        return $this->render('home/user/blog_category.html.twig',[
            'category'=>$category,
        ]);
    }
    /**
     * @Route("/user-blog-category-add",name="user_blog_category_add")
     */
    public function category_add(Request $request)
    {
        $user = $this->getUser();
        $category = new Category();
        $form = $this->createForm(CategoryAddType::class,$category);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();
            $category->setUserid($user);
            $entityManager->persist($category);
            $entityManager->flush();
            $this->addFlash('success','新增成功');
            return $this->redirectToRoute('user_blog_category_add');
        }
       
        return $this->render('home/user/blog_category_add.html.twig',[
            'form'=>$form->createView(),
        ]);
    }
    /**
     * @Route("/user-blog-category-{slug<\d+>}-edit",name="user_blog_category_edit")
     */
    public function category_edit($slug,Request $request)
    {
        $user = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(Category::class);
        $category = $repository->find($slug);
        if(!$category || $category->getUserid()->getId() != $user->getId()){
            throw $this->createNotFoundException('没有找到相关分类');
        }
        $form = $this->createForm(CategoryAddType::class,$category);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $entityManager->flush();
            $this->addFlash('success','编辑成功');
            return $this->redirectToRoute('user_blog_category');
        }
        return $this->render('home/user/blog_category_edit.html.twig',[
            'form'=>$form->createView()
        ]);
    }
    /**
     * @Route("/user-blog-category-{slug<\d+>}-delete",name="user_blog_category_delete")
     */
    public function category_delete($slug)
    {
        $user = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(Category::class);
        $category = $repository->find($slug);
        if(!$category || $category->getUserid()->getId() != $user->getId()){
            throw $this->createNotFoundException('没有找到相关分类');
        }
        $entityManager->remove($category);
        $entityManager->flush();
        $this->addFlash('success','删除成功');
        return $this->redirectToRoute('user_blog_category');   
    }
    /**
     * @Route("/user-blog-tag",name="user_blog_tag")
     */
    public function tag_index()
    {
        $user = $this->getUser();
        $tags = $user->getTags();
        return $this->render('home/user/blog_tag.html.twig',[
            'tags'=>$tags,
        ]);
    }
    /**
     * @Route("/user-blog-tag-add",name="user_blog_tag_add")
     */
    public function tag_add(Request $request)
    {
        
        $user = $this->getUser();
        $tag = new Tag();
        $form = $this->createForm(CategoryAddType::class,$tag); //公用表单
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $tag->setUserid($user);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($tag);
            $entityManager->flush();
            $this->addFlash('success','新增成功');
            return $this->redirectToRoute('user_blog_tag');
        }
        return $this->render('home/user/blog_tag_add.html.twig',[
            'form'=>$form->createView(),
        ]);
    }
    /**
     * @Route("/user-blog-tag-{slug<\d+>}-edit",name="user_blog_tag_edit")
     */
    public function tag_edit(Request $request,$slug)
    {        
        $user = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(Tag::class);
        $tag = $repository->find($slug);
        if(!$tag || $tag->getUserid()->getId() != $user->getId()){
            throw $this->createNotFoundException('没有找到相关标签');
        }
        $form = $this->createForm(CategoryAddType::class,$tag);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $entityManager->flush();
            $this->addFlash('success','编辑成功');
            return $this->redirectToRoute('user_blog_tag');
        }
        return $this->render('home/user/blog_tag_edit.html.twig',[
            'form'=>$form->createView(),
        ]);
    }
    /**
     * @Route("/user-blog-tag-{slug<\d+>}-delete",name="user_blog_tag_delete")
     */
    public function tag_delete($slug)
    {
        $user = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(Tag::class);
        $tag = $repository->find($slug);
        if(!$tag || $tag->getUserid()->getId() != $user->getId())
        {
            throw $this->createNotFoundException('没有找到相关标签');
        }
        $entityManager->remove($tag);
        $entityManager->flush();
        $this->addFlash('success','删除成功');
        return $this->redirectToRoute('user_blog_tag');
    }

    /**
     * @Route("/user-blog-post-{page<\d+>?1}-list",name="user_blog_post")
     */
    public function blog_post($page)
    {        
        $limit = 5;
        $user = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $queryBuilder = $entityManager->getRepository(Post::class)->createQueryBuilder('u')
                    ->where('u.userid = :userid')
                    ->setParameter('userid',$user->getId())
        ;
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($queryBuilder,$page,$limit);

        return $this->render('home/user/blog_post.html.twig',[
            'pagination'=>$pagination,
        ]);
    }
    /**
     * @Route("/user-blog-post-add",name="user_blog_post_add")
     */
    public function blog_post_add(Request $request)
    {
        $user = $this->getUser();
        $post = new Post();
        $form = $this->createForm(PostType::class,$post);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $post->setUserid($user);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            $user->setCountPost($user->getCountPost() + 1);
            $entityManager->persist($user);
            $entityManager->flush(); 

            $this->addFlash('success','新增博客文章成功');
            return $this->redirectToRoute('user_blog_post_add');
        }
        return $this->render('home/user/blog_post_add.html.twig',[
            'form'=> $form->createView(),
        ]);
    }
    /**
     * @Route("/user-blog-post-{slug<\d+>}-edit",name="user_blog_post_edit")
     */
    public function blog_post_edit(Request $request,$slug)
    {
        $user = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(Post::class);
        $post = $repository->find($slug);
        if(!$post || $post->getUserid()->getId() != $user->getId())
        {
            throw $this->createNotFoundException('没有找到相关文章');
        }
        $form = $this->createForm(PostType::class,$post);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager->flush();
            $this->addFlash('success','编辑成功');
            return $this->redirectToRoute('user_blog_post');
        }
        return $this->render('home/user/blog_post_edit.html.twig',[
            'form'=> $form->createView(),
        ]);
    }
    /**
     * @Route("/user-blog-post-{slug<\d+>}-delete",name="user_blog_post_delete")
     */
    public function blog_post_delete($slug)
    {
        $user = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(Post::class);
        $post = $repository->find($slug);
        if(!$post || $post->getUserid()->getId() != $user->getId())
        {
            throw $this->createNotFoundException('没有找到相关文章');
        }
        $entityManager->remove($post);
        $entityManager->flush();

        $user->setCountPost($user->getCountPost() - 1);
        $entityManager->persist($user);
        $entityManager->flush(); 

        $this->addFlash('success','删除成功');
        return $this->redirectToRoute('user_blog_post');
    }
}