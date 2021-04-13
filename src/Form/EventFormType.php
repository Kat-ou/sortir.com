<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
            ->add('name', TextType::class, [
                'label' => 'Nom de la sortie: '
            ])
            ->add('startDate', DateTimeType::class, [
                'label' => 'Date et heure de la sortie:  ',
                'date_widget' => 'single_text',
                'time_widget'=> 'single_text',
                'attr' =>['class' => 'has-text-link']
            ])
            ->add('deadLine', DateType::class, [
                'label' => "Date limite d'inscription: ",
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
            ])
            ->add('maxRegistrations', null, [
                'label' => 'Nombre de places: ',
            ])
            ->add('duration', null, [
                'label' => 'Durée: ',
            ])
            ->add('description', null, [
                'label' => "Description et infos: ",
            ])
            ->add('location', ChoiceType::class, [
                'label' => "Lieu: ",
                'mapped' => false,
            ])/*
            ->add('location', EntityType::class, [
                'label' => "Lieu: ",
                'class' => Lieu::class,
                'choice_label' => 'name'
            ])*/
            ->add('street', ChoiceType::class, [
                'label' => "Rue: ",
                'mapped' => false
            ])
            ->add('latitude', TextType::class, [
                'label' => "Latitude: ",
                'mapped' => false
            ])
            ->add('longitude', TextType::class, [
                'label' => "Longitude: ",
                'mapped' => false
            ])
            ->add('ville', EntityType::class, [
                'label' => "Ville: ",
                'class' => Ville::class,
                'mapped' => false,
                'choice_label' => 'name',
            ])
            ->add('postcode', ChoiceType::class, [
                'label' => "Code Postal: ",
                'mapped' => false
            ])

            /* champs inséré en dur dans créer une sortie*/
            ->add('campus', EntityType::class, [
                'label' => "Campus: ",
                'class' => Campus::class,
                'mapped' => false,
                'choice_label' => 'name',
                //'attr' =>['class' => 'has-text-link']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
            'validation_groups' => false,
        ]);
    }
}
