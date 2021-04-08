<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label'=>'Nom de la sortie:  '])
            ->add('startDate', DateType::class, [
                'label'=>'Date et heure de la sortie:  ',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
            ],
                DateTimeType::class, [
                    'widget' => 'choice',
            ])
            ->add('deadLine', DateType::class, [
                'label'=>"Date limite d'inscription: ",
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',

            ])
            ->add('duration')
            ->add('maxRegistrations')
            ->add('description')
            ->add('location', EntityType::class, [
                'class' => Lieu::class,
                'choice_label' => 'name'
            ])
            ->add('street', EntityType::class, [
                'class' => Lieu::class,
                'mapped' => false,
                'choice_label' => 'street'
            ])
            ->add('latitude', EntityType::class, [
                'class' => Lieu::class,
                'mapped' => false,
                'choice_label' => 'latitude'
            ])
            ->add('longitude', EntityType::class, [
                'class' => Lieu::class,
                'mapped' => false,
                'choice_label' => 'longitude'
            ])
            ->add('ville', EntityType::class, [
                'class' => Ville::class,
                'mapped' => false,
                'choice_label' => 'name'
            ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
