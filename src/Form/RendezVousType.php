<?php

namespace App\Form;

use App\Entity\RendezVous;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RendezVousType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date',DateTimeType::class,array(
                'required' => true,
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'datepicker datepicker-inline',
                    'data-provide' => 'datetimepicker',
                ],
            ))
            ->add('service',ChoiceType::class, [
                'choices' => [
                    'Services' => [
                        'Procuration' => 'Procuration',
                        'Visa' => 'Visa',
                        'Passeport' => 'Passeport',
                        'Heritage' => 'Heritage',
                        'Certificat divers' => 'Certificat divers',
                    ],
                ],])
            ->add('horaire')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RendezVous::class,
        ]);
    }
}
