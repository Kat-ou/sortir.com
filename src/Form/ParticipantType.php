<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParticipantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'Pseudo: ',
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom: ',
            ])
            ->add('name', TextType::class, [
                'label' => 'Nom: ',
            ])
            ->add('phone', TextType::class, [
                'label' => 'Téléphone: ',
            ])
            ->add('email', TextType::class, [
                'label' => 'Email: ',
            ])

            // à modifier
            ->add('roles', ChoiceType::class, [
                'label' => "Role: ",
                'mapped' => false,
                'choices' => [
                    'ROLE_USER' => 'ROLE_USER',
                    'ROLE_ADMIN' => 'ROLE_ADMIN'
                ]
            ])
            ->add('isActive', ChoiceType::class, [
                'label' => "Statut du compte: ",
                'choices' => [
                    'Actif' => true,
                    'Inactif' => false]
            ])
            ->add('campus', EntityType::class, [
                'label' => 'Campus: ',
                'class' => Campus::class,
                "choice_label" => "name",
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
