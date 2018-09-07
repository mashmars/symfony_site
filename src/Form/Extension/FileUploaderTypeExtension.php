<?php
/**
 * 
 */
namespace App\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FileUploaderTypeExtension extends AbstractTypeExtension
{
    public function getExtendedType()
    {
        return FileType::class;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefined(['image_property']);
    }
    public function buildView(FormView $view,FormInterface $form,array $options)
    {
        if(isset($options['image_property']))
        {
            $parentData = $form->getParent()->getData();
            $imageUrl = null;
            if(null !== $parentData)
            {
                $accessor = PropertyAccess::createPropertyAccessor();
                $imageUrl = $accessor->getValue($parentData,$options['image_property']);
            }
            $view->vars['image_url'] = $imageUrl;
        }
    }
}