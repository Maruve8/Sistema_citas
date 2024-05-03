<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CitaFechaFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fechaHora', DateType::class, [
                'widget' => 'single_text',  // Seleccionar solo la fecha
                'label' => 'Fecha',
                'attr' => ['class' => 'fecha-selector']  // Clase para JavaScript
            ])
            
            ->add('save', SubmitType::class, ['label' => 'Confirmar fecha']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            
        ]);
    }
}
