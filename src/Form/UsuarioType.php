<?php

namespace App\Form;

use App\Entity\Usuario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UsuarioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nombre', TextType::class, ['label' => 'Nombre'])
        ->add('apellidos', TextType::class, ['label' => 'Apellidos'])
        ->add('dni', TextType::class, ['label' => 'DNI', 'required' => false])
        ->add('direccion', TextType::class, ['label' => 'Dirección', 'required' => false])
        ->add('email', TextType::class, ['label' => 'Email'])
        ->add('password', PasswordType::class, ['label' => 'Contraseña'])
        ->add('telefono', TextType::class, ['label' => 'Teléfono', 'required' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Usuario::class,
        ]);
    }
}
