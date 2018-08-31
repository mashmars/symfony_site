<?php
namespace App\Form\Home;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use App\Entity\User;

class UserProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder , array $options)
    {
        $builder->add('username',TextType::class,['label'=>'用户名','disabled'=>true])
                ->add('email',TextType::class,['label'=>'邮箱','disabled'=>true])
                ->add('phone',TextType::class,['label'=>'手机号','disabled'=>true])
                ->add('domain',TextType::class,['label'=>'博客域名'])
                ->add('gender',ChoiceType::class,[
                    'label'=>'性别',
                    'choices'=>['男'=>'1','女'=>'0'],
                    'multiple'=>false,
                    'expanded'=>true,
                    'data'=>1, //默认值
                ])
                ->add('age',TextType::class,['label'=>'年龄'])
                ->add('nickname',TextType::class,['label'=>'昵称'])
                ->add('resume',TextareaType::class,['label'=>'个人摘要'])
                ->add('submit',SubmitType::class,['label'=>'保存修改','attr'=>['class'=>'button small']])  
        ;
    }
    public function ConfiureOptions(OptionsResolver $resolver)
    {
        return $resolver->setDefaults([
            'data-class'    =>  User::class,
        ]);
    }

}