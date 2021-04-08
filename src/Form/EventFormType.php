<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use Doctrine\DBAL\Types\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('startDate')
            ->add('deadLine')
            ->add('duration')
            ->add('maxRegistrations')
            ->add('description')
            ->add('location', EntityType::class, [
                'class' => Lieu::class,
                'choice_label' => 'name'
            ])
            ->add('street', EntityType::class, [
                'class' => Lieu::class,
                'mapped' =>false,
                'choice_label' => 'street'
            ])
            ->add('latitude', EntityType::class, [
                'class' => Lieu::class,
                'mapped' =>false,
                'choice_label' => 'latitude'
            ])
            ->add('longitude', EntityType::class, [
                'class' => Lieu::class,
                'mapped' =>false,
                'choice_label' => 'longitude'
            ])
            ->add('ville', EntityType::class, [
                'class' => Ville::class,
                'mapped' =>false,
                'choice_label' => 'name'
            ])
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
