<?php
namespace App\Form\Admin;

use Symfony\Component\Form\AbstractType; 
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository; 

use App\Entity\ArticleCategory;

class ArticleCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder,array $options)
    {
        return $builder
                    //需要考虑的有两点，1，如何增加下拉框的空选在 ---请选择--- 可以使用jquery处理 2,上下级的在前端的显示问题
                    /* ->add('pid',EntityType::class,[
                        'label'=>'上级分类',
                        'choice_label'=>'name',
                        'class'=>ArticleCategory::class,
                        'query_builder'=>function(EntityRepository $er){
                            return $er->createQueryBuilder('u');
                        },
                        
                    ]) */
                    ->add('name',TextType::class,['label'=>'分类名称'])
                    ->add('submit',SubmitType::class,['label'=>'保存','attr'=>['class'=>'button small hollow']])
        ;
    }
    public function ConfiureOptions(OptionsResolver $resolver)
    {
        return $resolver->setDefaults([
            'data-class'    =>  ArticleCategory::class,
        ]);
    }
}