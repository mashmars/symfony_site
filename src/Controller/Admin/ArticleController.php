<?php
namespace App\Controller\Admin;

use App\Controller\Admin\BaseController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\ArticleCategory;
use App\Form\Admin\ArticleCategoryType;

use App\Entity\Article;


/**
 * @Route("/admin-article-")
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
            return $this->redirectToRoute('category_add');
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
            return $this->redirectToRoute('category_edit',['slug'=>$slug]);
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
        return $this->redirectToRoute('category_index');
    }

    ///////////////文章
    /**
     * @Route("article-{page<\d+>?1}-index" , name="article_index")
     */
    public function article_index($page)
    {
        $limit = 2;
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
    public function article_add()
    {
        return $this->render('admin/article/article_add.html.twig');
    }
    /**
     * @Route("article-{slug}-edit",name="article_edit")
     */
    public function article_edit()
    {
        return $this->render('admin/article/article_edit.html.twig');
    }
    /**
     * @Route("article-{slug}-delete",name="article_delete")
     */
    public function article_delete()
    {
        return $this->render('admin/article/article_edit.html.twig');
    }








}