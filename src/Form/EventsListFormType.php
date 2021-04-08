<?php

namespace App\Form;

use App\Entity\Campus;
use App\Model\SearchForm;
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
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('campus', EntityType::class, [
                'label' => 'Campus ',
                'class' => Campus::class,
                'choice_label' => 'name',
                'choice_value' => 'id',
                'required' => false,
            ])
            ->add('searchInputText', TextType::class, [
                'label' => 'Le nom de la sortie contient : ',
                'constraints' => [
                    new Length([ 'min' => 3, 'max' => 250 ]),
                ],
                'attr' => ['placeholder' => 'recherche'],
                'required' => false,
            ])
            ->add('startDate', DateType::class, [
                'label' => 'Entre ',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'required' => false,
            ])
            ->add('endDate', DateType::class, [
                'label' => ' et  ',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'required' => false,
            ])
            ->add('isItMeOrganizer', CheckboxType::class, [
                'label' => 'Sorties dont je suis l\'organisateur(trice)',
                'attr' => ['checked' => 'checked'],
                'required' => false,
            ])
            ->add('isItMeRegister', CheckboxType::class, [
                'label' => 'Sorties auxquelles je suis inscrit(e)',
                'attr' => ['checked' => 'checked'],
                'required' => false,
            ])
            ->add('isItMeNoRegister', CheckboxType::class, [
                'label' => 'Sorties auxquelles je ne suis pas inscrit(e)',
                'attr' => ['checked' => 'checked'],
                'required' => false,
            ])
            ->add('isItEventsDone', CheckboxType::class, [
                'label' => 'Sorties passées',
                'required' => false,
            ])
        ;





    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SearchForm::class,
        ]);
    }
}
