<?php
namespace App\Form\Home;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository; 

use App\Entity\Post;
use App\Entity\Category;
use App\Entity\Tag;

//获取自己的分类
use Symfony\Component\Security\Core\Security;

class PostType extends AbstractType
{   
    //需要找属于自己的分类
    private $user;
    public function __construct(Security $security)
    {
        $this->user = $security->getUser();
    }
    public function buildForm(FormBuilderInterface $builder,array $options)
    {
        $builder->add('title',TextType::class,['label'=>'标题'])
                ->add('category',EntityType::class,[
                    'label'=>'分类',   
                    'choice_label'=>'name',                 
                    'class'=>Category::class,
                    'query_builder'=>function(EntityRepository $er){
                        return $er->createQueryBuilder('u')
                            ->where('u.userid = :userid')
                            ->setParameter('userid',$this->user->getId())
                            ->orderBy('u.id','asc')
                        ;
                    }
                ])
                ->add('tags',EntityType::class,[
                    'label'=>'标签',
                    'choice_label'=>'name',
                    'class'=>Tag::class,
                    'query_builder'=>function(EntityRepository $er){
                        return $er->createQueryBuilder('u')
                            ->where('u.userid = :userid')
                            ->setParameter('userid',$this->user->getId())
                        ;
                    },
                    'multiple'=>true,
                    'expanded'=>true,
                ])
                ->add('descript',TextAreaType::class,['label'=>'描述'])
                ->add('is_comment',ChoiceType::class,[
                    'label'=>'是否允许评论',
                    'choices'=>['允许'=>1,'不允许'=>0],
                    'multiple'=>false,
                    'expanded'=>true,
                   // 'data'=>1, //编辑时也会带这个默认值
                ])
                ->add('status',ChoiceType::class,[
                    'label'=>'是否显示',
                    'choices'=>['显示'=>1,'不显示'=>0],
                    'multiple'=>false,
                    'expanded'=>true,
                  //  'data'=>1,//编辑时也会带这个默认值
                ])
                ->add('content',TextAreaType::class,['label'=>'博客内容'])
                ->add('submit',SubmitType::class,['label'=>'保存','attr'=>['class'=>'button small margin-top-1']])
        ;
    }
    public function ConfiureOptions(OptionsResolver $resolver)
    {
        return $resolver->setDefaults([
            'data-class'    =>  Post::class,
        ]);
    }
}