<?php

namespace App\Form;

use App\Entity\Campus;
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
            ->add('name', TextType::class, ['label' => 'Nom de la sortie:  '])
            ->add('startDate', DateType::class, [
                'label' => 'Date et heure de la sortie:  ',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
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
                'attr' => array(
                    'placeholder' => 'minutes'
                )
            ])
            ->add('description', null, [
                'label' => "Description et infos: ",
            ])
            ->add('location', EntityType::class, [
                'label' => "Lieu: ",
                'class' => Lieu::class,
                'choice_label' => 'name'
            ])
            ->add('street', EntityType::class, [
                'label' => "Rue: ",
                'class' => Lieu::class,
                'mapped' => false,
                'choice_label' => 'street'
            ])
            ->add('latitude', EntityType::class, [
                'label' => "Latitude: ",
                'class' => Lieu::class,
                'mapped' => false,
                'choice_label' => 'latitude'
            ])
            ->add('longitude', EntityType::class, [
                'label' => "Longitude: ",
                'class' => Lieu::class,
                'mapped' => false,
                'choice_label' => 'longitude'
            ])
            ->add('ville', EntityType::class, [
                'label' => "Ville: ",
                'class' => Ville::class,
                'mapped' => false,
                'choice_label' => 'name'
            ])
            ->add('postcode', EntityType::class, [
                'label' => "Code Postal: ",
                'class' => Ville::class,
                'mapped' => false,
                'choice_label' => 'postcode'
            ])
            ->add('campus', EntityType::class, [
                'label' => "Campus: ",
                'class' => Campus::class,
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
