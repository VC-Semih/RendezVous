<?php

namespace App\Form;

use App\Entity\LockDate;
use Doctrine\DBAL\Types\DateTimeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Date;

class LockedDateFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('locked_date', DateType::class, [
                'label'=>' ',
                'required' => true,
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'datepicker datepicker-inline',
                    'data-provide' => 'datetimepicker',
                ],
//                'years' => range(date('Y'), date('Y') + 1)
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => LockDate::class,
        ]);
    }
}
