<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\EventFormType;
use App\Form\EventsListFormType;
use App\Model\SearchForm;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;


class MainController extends AbstractController
{

    /**
     * @Route("/", name="main_eventsList")
     */
    public function eventsList(Request $request, SortieRepository $sortieRepository, PaginatorInterface $paginator, SessionInterface $session): Response
    {
        // On creer le formulaire de recherches dans le cas ou la recherche n'est pas trouvé en session (pagination)
        $searchForm = ($session->get('memorySearch') == null) ? new SearchForm() : $session->get('memorySearch');

        // Association de l'objet SearchForm avec le formulaire
        $searchEventsForm = $this->createForm(EventsListFormType::class, $searchForm);

        // On ecoute la soumission du formulaire :
        $searchEventsForm->handleRequest($request);

        // Dans le cas ou le formulaire est soumis et validé
        if ( $searchEventsForm->isSubmitted() && $searchEventsForm->isValid() ) {
            // on garde en mémoire les sélections utilisateur en session pour permettre la navigation à l'aide de
            // la pagination et de retrouver sa dernière recherche en cas de retour sur la page d'accueil.
            $session->set('memorySearch', $searchForm);
        }

        // on va chercher la liste des sorties selon les critères :
        $eventsListToDisplay = $sortieRepository->getEventsListSorted( $this->getUser(), $searchForm);

        // pagination sur la liste "eventListToDisplay" avec une limite de 10 éléments par page (page transmise via l'url - page 1 par defaut)
        // (doc. https://github.com/KnpLabs/KnpPaginatorBundle)
        $events = $paginator->paginate(
            $eventsListToDisplay,
            $request->query->getInt('page', 1),
            10,
        );

        return $this->render('main/eventsList.html.twig', [
            'eventsListForm' => $searchEventsForm->createView(),
            'eventsList' => $events,
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

    /**
     * @Route ("/event/renounce/{id}",name="main_renounce", requirements={"id"="\d+"})
     */
    public function renounce($id, EntityManagerInterface $em, Request $request, SortieRepository $sortieRepository): Response
    {
        // on récupère la sortie
        /** @var Sortie $event */
        $event = $sortieRepository->findAllElementsByEvent($id);

        // on contrôle bien que l'utilisateur est bien inscrit & que la sortie est bien cloturée ou ouverte
        $eventState = $event->getState()->getWording();
        $isItRegister = $event->isItParticipantOfEvent($this->getUser());
        if ( $isItRegister && ( $eventState === "Ouverte" || $eventState === "Clôturée" ) ) {

            /** @var Participant $participant */
            $participant = $this->getUser();

            // on se retire de la liste des participants
            $event->removeParticipant($participant);

            // on exécute la requete
            $em->persist($event);
            $em->flush();
        }
        return $this->redirectToRoute('main_eventsList');
    }

}
















