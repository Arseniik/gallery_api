<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nickname');
        $builder->add('mail', EmailType::class);
        $builder->add('plainPassword');
        $builder->add('roles');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'      => 'AppBundle\Entity\User',
            'csrf_protection' => false,
            'roles'           => 'ROLE_USER'
        ]);
    }
}
?>
