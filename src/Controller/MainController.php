<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Form\EventFormType;
use App\Form\EventsListFormType;
use App\Model\SearchForm;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


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
        $searchForm = new SearchForm();

        // Association de l'objet SearchForm avec le forulaire
        $searchEventsForm = $this->createForm(EventsListFormType::class, $searchForm);

        // Selection auto du Campus de l'utilisateur connecté
        $currentCampus = $this->getUser()->getCampus();
        $searchEventsForm->get('campus')->setData($currentCampus);

        // On ecoute la soumission du formulaire :
        $searchEventsForm->handleRequest($request);

        // Dans le cas ou le formulaire est soumis et validé
        if ( $searchEventsForm->isSubmitted() && $searchEventsForm->isValid() ) {
            // on va chercher la liste des sorties selon les critères :
            $eventsListToDisplay = $sortieRepository->getEventsListSorted( $this->getUser(), $searchForm);
        }

        return $this->render('main/eventsList.html.twig', [
            'eventsListForm' => $searchEventsForm->createView(),
            'eventsList' => $eventsListToDisplay,
        ]);

    }

    /**
     * @Route("/event/details/{id}", name="details")
     */
    public function details($id, SortieRepository $sortieRepository,Request $request, EntityManagerInterface $entityManager): Response
    {
        //récupère la sortie souhaitée
        $event = $sortieRepository->findAllElementsByEvent($id);
        return $this->render('main/details.html.twig', ["sortie" => $event]);
    }

    /**
     * @Route ("/event/create",name="create")
     */
    public function create(EntityManagerInterface $entityManager, Request $request): Response
    {
        $event = new Sortie();
        $location = new Lieu();
        // on récupère des données dans l'instance location
        $currentName = $location->getName();
        $currentStreet = $location->getStreet();
        $currentLatitude = $location->getLatitude();
        $currentLongitude = $location->getLongitude();
        $currentLocationCity = $location->getCity();
        $eventForm = $this->createForm(EventFormType::class, $event);

        $eventForm->get('name')->setData($currentName);
        $eventForm->get('street')->setData($currentStreet);
        $eventForm->get('latitude')->setData($currentLatitude);
        $eventForm->get('longitude')->setData($currentLongitude);
        $eventForm->get('ville')->setData($currentLocationCity);

        $eventForm->handleRequest($request);
        if ($eventForm->isSubmitted() && $eventForm->isValid()) {
            $event->setName($eventForm->get('name')->getData());
            $event->setStreet($eventForm->get('street')->getData());
            $event->setLatitude($eventForm->get('latitude')->getData());
            $event->setLongitude($eventForm->get('longitude')->getData());
            $entityManager->persist($event);
            $entityManager->flush();
        }
        return $this->render('main/create.html.twig', [
            'eventForm' => $eventForm->createView()
        ]);
    }


}
