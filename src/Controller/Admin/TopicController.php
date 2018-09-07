<?php 
namespace App\Controller\Admin;

use App\Controller\Admin\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use App\Entity\TopicCategory;
/**
 * @Route("/admin-topic-",name="admin_topic_")
 */
class TopicController extends BaseController
{
    /**
     * @Route("category-index",name="category_index")
     */
    public function category()
    {
        $repository = $this->getDoctrine()->getManager()->getRepository(TopicCategory::class);
        $categorys = $repository->findAll();
        return $this->render('admin/topic/category.html.twig',[
            'categorys' =>  $categorys,
        ]);
    }
    /**
     * @Route("category-add",name="category_add")
     */
    public function category_add(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $category = new TopicCategory();
        $form = $this->createFormBuilder($category)
                    ->add('name',TextType::class,['label'=>'名称'])
                    ->add('submit',SubmitType::class,['label'=>'提交','attr'=>['class'=>'button small ']])
                    ->getForm()
                ;
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager->persist($category);
            $entityManager->flush();
            $this->addFlash('success','新增成功');
            return $this->redirectToRoute('admin_topic_category_add');
        }
        return $this->render('admin/topic/category_add.html.twig',[
            'form'  =>  $form->createView(),
        ]);
    }
    /**
     * @Route("category-{id<\d+>}-edit",name="category_edit")
     */
    public function category_edit($id,Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(TopicCategory::class);
        $category = $repository->find($id);
        if(!$category){
            throw $this->createNotFoundException('没有找到相关分类');
        }
        $form = $this->createFormBuilder($category)
                    ->add('name',TextType::class,['label'=>'名称'])
                    ->add('submit',SubmitType::class,['label'=>'提交','attr'=>['class'=>'button small ']])
                    ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager->flush();
            $this->addFlash('success','分类编辑成功');
            $this->redirectToRoute('admin_topic_category_edit',['id'=>$id]);
        }
        return $this->render('admin/topic/category_edit.html.twig',[
            'form'  =>  $form->createView()
        ]);
    }
    /**
     * @Route("category-{id<\d+>}-delete",name="category_delete")
     */
    public function category_delete($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(TopicCategory::class);
        $category = $repository->find($id);
        if(!$category){
            throw $this->createNotFoundException('没有找到相关分类');
        }
        $entityManager->remove($category);
        $entityManager->flush();
        $this->addFlash('success','分类删除成功');
        $this->redirectToRoute('admin_topic_category_index');
    }
}



