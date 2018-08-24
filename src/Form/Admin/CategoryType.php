<?php
/**
 * Created by PhpStorm.
 * User: mash
 * Date: 2018/7/2
 * Time: 18:39
 */
namespace App\Form\Admin;

use App\Entity\Category;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',TextType::class,['label'=>'名称'])
            ->add('submit',SubmitType::class,['label'=>'提交'])
            ;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        return $resolver->setDefaults([
           'data-class' =>  Category::class,
        ]);
    }
}