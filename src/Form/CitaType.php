<?php

namespace App\Form;

use App\Entity\Cita;
use App\Entity\medico;
use App\Entity\usuario;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CitaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fechaHora', null, [
                'widget' => 'single_text',
            ])
            ->add('estado')
            ->add('paciente', EntityType::class, [
                'class' => usuario::class,
                'choice_label' => 'id',
            ])
            ->add('medico', EntityType::class, [
                'class' => medico::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cita::class,
        ]);
    }
}
