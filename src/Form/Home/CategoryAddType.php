<?php 
namespace App\Form\Home;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use App\Entity\Category;

class CategoryAddType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder,array $options)
    {
        $builder->add('name',TextType::class,['label'=>'名称'])
                ->add('submit',SubmitType::class,['label'=>'新增','attr'=>['class'=>'button small']])
        ;
    }

    public function ConfiureOptions(OptionsResolver $resolver)
    {
        return $resolver->setDefaults([
            'data-class'    =>  Category::class,
        ]);
    }
}