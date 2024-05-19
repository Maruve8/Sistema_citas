<?php

namespace App\Form;

use App\Entity\Usuario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre', TextType::class, [
                'label' => 'Nombre',
                'attr' => ['placeholder' => 'Nombre']
            ])
            ->add('apellidos', TextType::class, [
                'label' => 'Apellidos',
                'attr' => ['placeholder' => 'Apellidos']
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => ['placeholder' => 'Email']
            ])
            ->add('telefono', TextType::class, [
                'label' => 'Teléfono',
                'attr' => ['placeholder' => 'Teléfono']
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => 'Contraseña',
                    'attr' => ['placeholder' => 'Contraseña']
                ],
                'second_options' => [
                    'label' => 'Repetir Contraseña',
                    'attr' => ['placeholder' => 'Repetir Contraseña']
                ],
            ])
            ->add('termsAccepted', CheckboxType::class, [
                'mapped' => false,
                'label' => 'Acepto los términos y condiciones',
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Usuario::class,
        ]);
    }
}


