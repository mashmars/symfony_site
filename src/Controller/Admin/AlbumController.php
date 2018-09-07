<?php
namespace App\Controller\Admin;

use App\Controller\Admin\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Album;
use App\Entity\AlbumCategory;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * @Route("/admin-album-",name="admin_album_")
 */
class AlbumController extends BaseController
{
    /**
     * @Route("category-index",name="category_index")
     */
    public function category_index()
    {
        $repository = $this->getDoctrine()->getManager()->getRepository(AlbumCategory::class);
        $categorys = $repository->findAll();
        return $this->render('admin/album/category.html.twig',[
            'categorys' =>  $categorys,
        ]);
    }
    /**
     * @Route("category-add",name="category_add")
     */
    public function category_add(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $category = new AlbumCategory();
        $form = $this->createFormBuilder($category)
                ->add('name',TextType::class,['label'=>'分类名称'])
                ->add('submit',SubmitType::class,['label'=>'提交','attr'=>['class'=>'button small']])
                ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($category);
            $entityManager->flush();
            $this->addFlash('success','新增成功');
            return $this->redirectToRoute('admin_album_category_add');
        }
        return $this->render('admin/album/category_add.html.twig',[
            'form'  =>  $form->createView(),
        ]);
    }
    /**
     * @Route("category-{id<\d+>}-edit",name="category_edit")
     */
    public function category_edit($id,Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(AlbumCategory::class);
        $category = $repository->find($id);
        if(!$category) throw $this->createNotFoundException('没有找到相关分类');
        $form = $this->createFormBuilder($category)
                ->add('name',TextType::class,['label'=>'分类名称'])
                ->add('submit',SubmitType::class,['label'=>'提交','attr'=>['class'=>'button small']])
                ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){            
            $entityManager->flush();
            $this->addFlash('success','编辑成功');
            return $this->redirectToRoute('admin_album_category_edit',['id'=>$id]);
        }
        return $this->render('admin/album/category_edit.html.twig',[
            'form'  =>  $form->createView(),
        ]);
    }
    /**
     * @Route("category-{id<\d+>}-delete",name="category_delete",methods={"POST"})
     */
    public function category_delete($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(AlbumCategory::class);
        $category = $repository->find($id);
        if(!$category) throw $this->createNotFoundException('没有找到相关分类');
        $entityManager->remove($category);       
        $entityManager->flush();
        $this->addFlash('success','编辑成功');
        return $this->redirectToRoute('admin_album_category_index');        
    }
}