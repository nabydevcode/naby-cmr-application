<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlombSearchTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('start', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
                'label' => 'Date de début'
            ])
            ->add('end', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
                'label' => 'Date de fin'
            ]);
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'GET', // Important !
            'csrf_protection' => false, // Pas nécessaire pour GET
        ]);
    }

}
