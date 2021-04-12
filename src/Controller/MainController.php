<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\EventFormType;
use App\Form\EventsListFormType;
use App\Model\SearchForm;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use App\Services\RefreshStatesEvents;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;


class MainController extends AbstractController
{

    /**
     * @Route("/", name="main_eventsList")
     */
    public function eventsList(Request $request, SortieRepository $sortieRepository, PaginatorInterface $paginator,
                               SessionInterface $session, RefreshStatesEvents $refreshStatesEvents): Response
    {
        // Appel au service "RefreshStatesEvents" pour Mettre certaines données à jour en base.
        $refreshStatesEvents->refreshStateEventsIntoDb();

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
            // parametrage de la premiere page pour une nouvelle recherche (empeche de bloqué sur une page inexistante)
            $request->query->set('page', 1);
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

        // Affichage des sorties dans la vue 'twig'
        // (doc. https://twig.symfony.com/doc/2.x/filters/u.html)
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
    public function create(EntityManagerInterface $entityManager, Request $request, EtatRepository $etatRepository): Response
    {
        $event = new Sortie();

        // On récupère le nom du campus
        $currentCampus = $this->getUser()->getCampus()->getName();

        // On créé le formulaire
        $eventForm = $this->createForm(EventFormType::class, $event);

        $eventForm->handleRequest($request);

        if ($eventForm->isSubmitted() && $eventForm->isValid()) {
            $event->setOrganizer($this->getUser());
            $event->setOrganizingSite($this->getUser()->getCampus());
            $event->setLocation($eventForm->get('location')->getData());
            //$event->setState()

            $entityManager->persist($event);
            $entityManager->flush();
        }
        return $this->render('main/create.html.twig', [
            'eventForm' => $eventForm->createView(),
            'nomCampus' =>$currentCampus
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
        if ( $event->isItPossibleToRenounce($this->getUser()) ) {

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

    /**
     * @Route ("/event/create/location/{id}", name="main_location")
     */
    public function getLocation($id)
    {




        return new JsonResponse(['id' => $id]);
    }

    /**
     * @Route ("/event/register/{id}",name="main_register", requirements={"id"="\d+"})
     */
    public function register($id, EntityManagerInterface $em, Request $request, SortieRepository $sortieRepository): Response
    {
        // on récupère la sortie
        /** @var Sortie $event */
        $event = $sortieRepository->findAllElementsByEvent($id);

        // on contrôle qu'il reste de la place dans la sortie & que la sortie est bien ouverte
        if ( $event->isItPossibleToRegister($this->getUser()) ) {

            /** @var Participant $participant */
            $participant = $this->getUser();

            // on s'ajoute à la liste des participants
            $event->addParticipant($participant);

            // on exécute la requete
            $em->persist($event);
            $em->flush();
        }
        return $this->redirectToRoute('main_eventsList');
    }

}


























