<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class EventsListFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $searchEventsForm, array $options)
    {

        $searchEventsForm
            ->add('campus', EntityType::class, [
                'label' => 'Campus ',
                'class' => Campus::class,
                'choice_label' => 'name',
            ])
            ->add('search', TextType::class, [
                'label' => 'Le nom de la sortie contient : ',
                'constraints' => new Length([
                    'min' => 3,
                    'max' => 250
                ]),
                'attr' => ['placeholder' => 'recherche']
            ])
            ->add('startDate', DateType::class, [
                'label' => 'Entre ',
            ])
            ->add('endDate', DateType::class, [
                'label' => ' et  ',
            ])
            ->add('meOrganizerChoice', CheckboxType::class, [
                'label' => 'Sorties dont je suis l\'organisateur(trice)',
                'attr' => ['checked' => 'checked']
            ])
            ->add('meRegisterChoice', CheckboxType::class, [
                'label' => 'Sorties auxquelles je suis inscrit(e)',
                'attr' => ['checked' => 'checked']
            ])
            ->add('meNoRegisterChoice', CheckboxType::class, [
                'label' => 'Sorties auxquelles je ne suis pas inscrit(e)',
                'attr' => ['checked' => 'checked']
            ])
            ->add('eventsDoneChoice', CheckboxType::class, [
                'label' => 'Sorties passées',
            ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}
