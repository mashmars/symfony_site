<?php
namespace App\Controller\Admin;

use App\Controller\Admin\BaseController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\ArticleCategory;
use App\Form\Admin\ArticleCategoryType;

use App\Entity\Article;
use App\Form\Admin\ArticleType;

use App\Services\FileUploader;
use Symfony\Component\HttpFoundation\File\File;
/**
 * @Route("/admin-article-",name="admin_")
 */
class ArticleController extends BaseController
{
    /**
     * @Route("category-index",name="category_index")
     */
    public function category_index()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(ArticleCategory::class);
        $categorys = $repository->findAll();
        return $this->render('admin/article/category.html.twig',[
            'categorys'=>$categorys,
        ]);
    }
    /**
     * @Route("category-add",name="category_add")
     */
    public function category_add(Request $request)
    {   
        $category = new ArticleCategory();
        $form = $this->createForm(ArticleCategoryType::class,$category);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();
            $this->addFlash('success','分类添加成功');
            return $this->redirectToRoute('admin_category_add');
        }
        return $this->render('admin/article/category_add.html.twig',[
            'form'=>$form->createView(),
        ]);
    }
    /**
     * @Route("category-{slug<\d+>}-edit",name="category_edit")
     */
    public function category_edit($slug,Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(ArticleCategory::class);
        $category = $repository->find($slug);
        if(!$category){
            throw $this->createNotFoundException('没有找到相关分类');
        }
        $form = $this->createForm(ArticleCategoryType::class,$category);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager->flush();
            $this->addFlash('success','分类编辑成功');
            return $this->redirectToRoute('admin_category_edit',['slug'=>$slug]);
        }
        return $this->render('admin/article/category_edit.html.twig',[
            'form'=>$form->createView(),
        ]);
    }
    /**
     * @Route("category-{slug<\d+>}-delete",name="category_delete")
     */
    public function category_delete($slug)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(ArticleCategory::class);
        $category = $repository->find($slug);
        if(!$category){
            throw $this->createNotFoundException('没有找到相关分类');
        }
        $entityManager->remove($category);
        $entityManager->flush();
        $this->addFlash('success','分类删除成功');
        return $this->redirectToRoute('admin_category_index');
    }

    ///////////////文章
    /**
     * @Route("article-{page<\d+>?1}-index" , name="article_index")
     */
    public function article_index($page)
    {
        $limit = 10;
        $repository = $this->getDoctrine()->getManager()->getRepository(Article::class);
        $queryBuilder = $repository->createQueryBuilder('u')->orderBy('u.id','desc');
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($queryBuilder,$page,$limit);
        return $this->render('admin/article/article.html.twig',[
            'pagination'=>$pagination,
        ]);
    }
    /**
     * @Route("article-add",name="article_add")
     */
    public function article_add(Request $request,FileUploader $fileUploader)
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class,$article);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            //$file = $article->getThumb(); //上面这个无效
            $file = $form->get('thumb')->getData();
            if($file){
                $filename = $fileUploader->upload($file);
                $filename = $request->getSchemeAndHttpHost() . '/' . $this->getParameter('upload.directory') . $filename;
            }else{
                $ss = mt_rand(1000000,9999999);
                $filename = 'https://www.gravatar.com/avatar/'.md5($ss).'?s=150&d=identicon&r=g';
            }
           
            $article->setThumb($filename);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();
            $this->addFlash('success','文章添加成功');
            return $this->redirectToRoute('admin_article_add');
        }
        return $this->render('admin/article/article_add.html.twig',[
            'form'=>$form->createView(),
        ]);
    }
    /**
     * @Route("article-{slug}-edit",name="article_edit")
     */
    public function article_edit($slug,Request $request,FileUploader $fileUploader)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(Article::Class);
        $article = $repository->find($slug);
        if(!$article)
        {
            throw $this->createNotFoundException('没有找到相关文章');
        }
        $oldimg = $article->getThumb();
        if($oldimg){
            //$article->setThumb(new File($article->getThumb()));
        }
        $form = $this->createForm(ArticleType::class,$article);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $file = $form->get('thumb')->getData();
            if($file){
                $filename = $fileUploader->upload($file);
                $filename = $request->getSchemeAndHttpHost() . '/' . $this->getParameter('upload.directory') . $filename;
                //删除原来的
                @unlink($oldimg);
            }else{
                if(!$oldimg){
                    $ss = mt_rand(1000000,9999999);
                    $oldimg = 'https://www.gravatar.com/avatar/'.md5($ss).'?s=150&d=identicon&r=g';
                }
                $filename = $oldimg;
            }
            
            $article->setThumb($filename);

            $entityManager->flush();
            $this->addFlash('success','文章编辑成功');
            return $this->redirectToRoute('admin_article_edit',['slug'=>$slug]);
        }
        return $this->render('admin/article/article_edit.html.twig',[
            'form'=>$form->createView(),
        ]);
    }
    /**
     * @Route("article-{slug}-delete",name="article_delete")
     */
    public function article_delete($slug)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(Article::class);
        $article = $repository->find($slug);

        $entityManager->remove($article);
        $entityManager->flush();

        if($article->getThumb()){
            @unlink($article->getThumb());
        }
        $this->addFlash('success','文章删除成功');
        return $this->redirectToRoute('admin_article_index');        
    }








}