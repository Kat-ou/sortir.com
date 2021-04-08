<?php

namespace App\Controller;

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
        $searchEventsForm = $this->createForm(EventsListFormType::class, $searchForm);

        // On ecoute la soumission du formulaire :
        $searchEventsForm->handleRequest($request);

        // Dans le cas ou le formulaire est soumis et validé
        if ( $searchEventsForm->isSubmitted() && $searchEventsForm->isValid() ) {

            // On vérifie si il y a une erreur pour la recherche
            $errors = $searchForm->getErrorsSearchForm();
            if (count($errors) > 0) {
                dump($errors);
                //TODO -> message flash !?
            } else {
                // on va chercher la liste des sorties selon les critères :
                $search = ($searchForm->getSearchInputText() !== null)? $searchForm->getSearchInputText() : null ;
                $eventsListToDisplay = $sortieRepository->getEventsListSorted(
                    $this->getUser(),
                    $searchForm->getCampus()->getId(),
                    $searchForm->getStartDate(),
                    $searchForm->getEndDate(),
                    $search,
                    $searchForm->isItMeOrganizer(),
                    $searchForm->isItMeRegister(),
                    $searchForm->isItMeNoRegister(),
                    $searchForm->isItEventsDone()
                );
            }

            // VarDumper
            dump($searchForm);
            dump($eventsListToDisplay);

        }

        return $this->render('main/eventsList.html.twig', [
            'eventsListForm' => $searchEventsForm->createView(),
            'eventsList' => $eventsListToDisplay,
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
