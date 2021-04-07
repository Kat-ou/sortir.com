<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Length;


class MainController extends AbstractController
{
    /**
     * @Route("/", name="main_eventsList")
     */
    public function eventsList(Request $request, SortieRepository $sortieRepository): Response
    {
        // Déclaration
        $eventsListToDisplay = [];

        // On creer le formulaire de recherches :
        $searchEventsForm = $this->createFormBuilder()
            ->add('campus', EntityType::class, [
                'label' => 'Campus ',
                'class' => Campus::class,
                'choice_label' => 'name',
                'choice_value' => 'id',
                'required' => false,
            ])
            ->add('search', TextType::class, [
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
            ->add('meOrganizerChoice', CheckboxType::class, [
                'label' => 'Sorties dont je suis l\'organisateur(trice)',
                'attr' => ['checked' => 'checked'],
                'required' => false,
            ])
            ->add('meRegisterChoice', CheckboxType::class, [
                'label' => 'Sorties auxquelles je suis inscrit(e)',
                'attr' => ['checked' => 'checked'],
                'required' => false,
            ])
            ->add('meNoRegisterChoice', CheckboxType::class, [
                'label' => 'Sorties auxquelles je ne suis pas inscrit(e)',
                'attr' => ['checked' => 'checked'],
                'required' => false,
            ])
            ->add('eventsDoneChoice', CheckboxType::class, [
                'label' => 'Sorties passées',
                'required' => false,
            ])
            ->getForm();
        // On ecoute la soumission du formulaire :
        $searchEventsForm->handleRequest($request);

        // Dans le cas ou le formulaire est soumis et validé
        if ( $searchEventsForm->isSubmitted() && $searchEventsForm->isValid() ) {
            // on récupère les saisies utilisateur (tableau) :
            $data = $request->request->get('form');
            $campusChoice = $data['campus'];
            $strSearchChoice = $data['search'];
            $startDateChoice = $data['startDate'];
            $endDateChoice = $data['endDate'];
            $meOrganizerChoice = $data['meOrganizerChoice'];
            $meRegisterChoice = $data['meRegisterChoice'];
            $meNoRegisterChoice = $data['meNoRegisterChoice'];
            $eventsDoneChoice = $data['eventsDoneChoice'];
            $startDateTimeChoice = \DateTime::createFromFormat('Y-m-d', $startDateChoice)->format('Y-m-d H:i:s');
            $endDateTimeChoice = \DateTime::createFromFormat('Y-m-d', $endDateChoice)->format('Y-m-d H:i:s');

            $eventsListToDisplay = $sortieRepository->getEventsListSorted($campusChoice, $startDateTimeChoice, $endDateTimeChoice, $strSearchChoice);


dd($eventsListToDisplay);



        }




        return $this->render('main/eventsList.html.twig', [
            'eventsListForm' => $searchEventsForm->createView(),
        ]);
    }


    /**
     * @Route("/details/{id}", name="details")
     */
    public function details($id, SortieRepository $sortieRepository,Request $request, EntityManagerInterface $entityManager): Response
    {
        //récupère la sortie souhaitée
        $event = $sortieRepository->findAllElementsByEvent($id);
        return $this->render('main/details.html.twig', ["sortie" => $event]);
    }

}
