<?php
namespace App\Form\Home;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;

use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

use Symfony\Component\Validator\Constraints\NotBlank;

use App\Entity\User;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder , array $options)
    {
        $builder->add('username',TextType::class,['label'=>'用户名*','attr'=>['placeholder'=>'用户名必填']])
                ->add('phone',TextType::class,['label'=>'手机号*'])                
                ->add('plainPassword',RepeatedType::class,[
                    'type'=> PasswordType::class,
                    'mapped'=>false,
                    'first_options'=>['label'=>'密码'],
                    //'first_options'=>['label'=>'密码','attr'=>['validate'=>NotBlank::class]],
                    'second_options'=>['label'=>'确认密码']
                ])
                ->add('email',EmailType::class,['label'=>'Email','attr'=>['class'=>'email']])
                ->add('sendEmail',ButtonType::class,['label'=>'发送邮件','attr'=>['class'=>'float-right send_email']])
                ->add('code',TextType::class,['label'=>'邮箱验证码','mapped'=>false,'attr'=>['require'=>true]])
                ->add('submit',SubmitType::class,['label'=>'注册','attr'=>['class'=>'button hollow primary small']])
        ;
        
    }

    public function ConfiureOptions(OptionsResolver $resolver)
    {
        return $resolver->setDefaults([
            'data-class'    =>  User::class,
        ]);
    }
}