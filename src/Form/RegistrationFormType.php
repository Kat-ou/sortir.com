<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', null, ['label' => 'Identifiant: '])
            ->add('email', EmailType::class,['label' => 'Email: '])
            /*->add('plainPassword', PasswordType::class, [
                'label'=>'Mot de passe:',
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez renseigner un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])*/
            ->add('campus', EntityType::class,[
                'label' => "Campus: ",
                'class' => Campus::class,
                'choice_label' => 'name',
            ])
            ->add('roles', ChoiceType::class,[
                'label' => "Role: ",
                'mapped' =>false,
                'choices' => [
                    'ROLE_USER'=>'ROLE_USER',
                    'ROLE_ADMIN'=>'ROLE_ADMIN'
                ]
            ])
            ->add('isActive',ChoiceType::class,[
                'label' => "Statut du compte: ",
                'choices' => [
                    'Actif'=>true,
                    'Inactif'=>false]
                ])

            ->add('name', TextType::class, ['label' => 'Nom:'])
            ->add('firstname', TextType::class, ['label' => 'Prénom:'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
