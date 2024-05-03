<?php

namespace App\Form;

use App\Entity\Especialidad;
use App\Entity\Medico;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Cita;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints as Assert;
use symfony\Component\Form\FormError;


class CitaOnlineType extends AbstractType
{

    



    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('especialidad', EntityType::class, [
                'class' => Especialidad::class,
                'choice_label' => 'nombre',
                'placeholder' => 'Escoge una especialidad',
                'mapped' => true,
                'attr' => ['class' => 'especialidad-selector'],
            ])
            ->add('medico', EntityType::class, [
                'class' => Medico::class,
                'choice_label' => function (Medico $medico) {
                    return $medico->getNombre() . ' ' . $medico->getApellidos();
                },
                'placeholder' => 'Primero selecciona una especialidad',
                
                'mapped' => true,
                'attr' => ['class' => 'medico-selector'],
            ]);
            
           // Agregar validación personalizada para evitar el error de the selected choice is invalid
        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();
                $medico = $form->get('medico')->getData();
                $especialidad = $form->get('especialidad')->getData();

                if ($medico && !$especialidad->getMedicos()->contains($medico)) {
                    $form->get('medico')->addError(new FormError("El médico seleccionado no es válido para la especialidad elegida."));
                }
            }
        );
            
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'post',
            //'data_class' => Cita::class,
    
        ]);
    }
}