<?php

namespace App\Form;

use App\Entity\Especialidad;
use App\Entity\Medico;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Cita;

class CitaOnlineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('especialidad', EntityType::class, [
                'class' => Especialidad::class,
                'choice_label' => 'nombre',
                'placeholder' => 'Escoge una especialidad',
                'mapped' => false,
                'attr' => ['class' => 'especialidad-selector'],
            ])
            ->add('medico', EntityType::class, [
                'class' => Medico::class,
                'choice_label' => function (Medico $medico) {
                    return $medico->getNombre() . ' ' . $medico->getApellidos();
                },
                'placeholder' => 'Primero selecciona una especialidad',
                'choices' => [],
            ]);

            
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'post',
            'data_class' => Cita::class,
        ]);
    }
}