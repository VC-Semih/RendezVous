<?php

namespace App\Form;

use App\Entity\RendezVous;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RendezVousType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date')
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
            ->add('user',RegistrationFormType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RendezVous::class,
        ]);
    }
}
