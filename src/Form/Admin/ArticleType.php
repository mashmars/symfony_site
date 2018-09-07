<?php
namespace App\Form\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

use App\Entity\Article;
use App\Entity\ArticleCategory;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder , array $options)
    {
        return $builder
                ->add('category',EntityType::class,[
                    'label'=>'文章分类',
                    'choice_label'=>'name',
                    'class'=>ArticleCategory::class,
                    'query_builder'=>function(EntityRepository $er){
                        return $er->createQueryBuilder('u')->orderBy('u.id','asc');
                    }
                ])
                ->add('title',TextType::class,['label'=>'文章标题'])
                ->add('descript',TextareaType::class,['label'=>'文章描述'])
                ->add('thumb',FileType::class,['label'=>'缩略图','image_property' => 'webPath','data_class'=>null])
                ->add('status',ChoiceType::class,[
                    'label'=>'是否显示',
                    'choices'=>[
                        '显示'=>1,
                        '不显示'=>0
                    ],
                    'multiple'=>false,
                    'expanded'=>true,
                ])
                ->add('content',TextareaType::class,['label'=>'文章内容'])
                ->add('submit',SubmitType::class,['label'=>'提交','attr'=>['class'=>'button small margin-top-1 margin-left-3']])
                ;
    }
    public function ConfiureOptions(OptionsResolver $resolver)
    {
        return $resolver->setDefaults([
            'data-class'    =>  Post::class,
        ]);
    }
}