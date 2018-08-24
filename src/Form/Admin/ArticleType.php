<?php
/**
 * Created by PhpStorm.
 * User: mash
 * Date: 2018/7/3
 * Time: 11:08
 */
namespace App\Form\Admin;

use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

use App\Entity\Article;
use App\Entity\Tag;
use App\Entity\Category;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',TextType::class,['label'=>'文章标题'])
            ->add('descript',TextareaType::class,['label'=>'描述'])
            /*->add('category',ChoiceType::class,[
                'choices'    =>  [
                    'Php' =>  '1',
                    'Py' =>  '2',
                ],
                'label' =>  '分类',
            ])*/
            /*//直接实体类查询
            ->add('category',EntityType::class,[
                'class' =>  Category::class,
                'choice_label' => 'name', //这个是category表的字段，对应option
                'label' =>'分类',
            ])*/
            //定制查询
            ->add('category',EntityType::class,[
                'label' => '分类',
                'class' => Category::class,
                'choice_label'  => 'name',
                'query_builder' =>  function(EntityRepository $er){
                    return $er->createQueryBuilder('u')
                        ->andWhere('u.level = :level')
                        ->setParameter('level',1)
                        ->orderBy('u.id','ASC')
                        ;
                },
            ])
            ->add('tags',EntityType::class,[
                'label' =>  '标签',
                'class' =>  Tag::class,
                'choice_label'  =>  'name',
                'expanded'  =>  true,
                'multiple'  =>  true,
            ])
            ->add('thumb',FileType::class,['label'=>'上传','data_class'=>null])
            ->add('is_comment',ChoiceType::class,[
                'label' =>  '是否评论',
                'choices'    =>  [
                    '可评论'   =>  1,
                    '不可评论'  =>  0,
                ],
                'expanded'  =>  true,
                'multiple'  =>  false,
            ])
            ->add('content',TextareaType::class,['label'=>'内容'])
            ->add('submit',SubmitType::class,['label'=>'提交'])
            ;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data-class'=>Article::class]);
    }
}