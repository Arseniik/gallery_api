<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class GalleryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('pictures', CollectionType::class, array(
            'entry_type' => 'AppBundle\Form\Type\Picture',
            'required'   => false,
            'empty_data' => []
        ));
        $builder->add('name');
        $builder->add('isCommonGallery');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'      => 'AppBundle\Entity\Gallery',
            'csrf_protection' => false,
            'roles'           => 'ROLE_USER'
        ]);
    }
}
?>
