<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\CsvFormType;
use App\Form\ParticipantType;
use App\Form\ProfileFormType;
use App\Form\RegistrationFormType;
use App\Model\CsvForm;
use App\Repository\CampusRepository;
use App\Repository\ParticipantRepository;
use App\Security\AppAuthenticator;
use App\Services\ImportParticipants;
use App\Services\PictureServices;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

//Test php bin/console make:crud

/**
 * @Route("/administration")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="admin_index", methods={"GET"})
     */
    public function index(ParticipantRepository $participantRepository): Response
    {
        return $this->render('admin/index.html.twig', [
            'participants' => $participantRepository->findAll(),
        ]);
    }

    // non utilisé - voir Registration Controller

    /**
     * @Route("/new", name="admin_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $participant = new Participant();
        $form = $this->createForm(ParticipantType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($participant);
            $entityManager->flush();

            return $this->redirectToRoute('admin_index');
        }

        return $this->render('admin/new.html.twig', [
            'participant' => $participant,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_show", methods={"GET"})
     */
    public function show(Participant $participant): Response
    {
        return $this->render('admin/show.html.twig', [
            'participant' => $participant,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Participant $participant, EntityManagerInterface $entityManager): Response
    {

        $form = $this->createForm(ParticipantType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $participant->setUpdatedDate(new \DateTime());

            $entityManager->persist($participant);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_index');
        }

        return $this->render('admin/edit.html.twig', [
            'participant' => $participant,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_delete", methods={"POST"})
     */
    public function delete(Request $request, Participant $participant): Response
    {
        if ($this->isCsrfTokenValid('delete' . $participant->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($participant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_index');
    }

    /**
     * @Route("/admin/register", name="app_register")
     */
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder, ParticipantRepository $participantRepository,
                             GuardAuthenticatorHandler $guardHandler, AppAuthenticator $authenticator, CampusRepository $campusRepository, ImportParticipants $importParticipants): Response
    {
        // Gestion Formulaire de creation d'un participant :
        $user = new Participant();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        // Gestion Formulaire d'import de plusieurs participants :
        $csvForm =  new CsvForm();
        $csvRegisterForm = $this->createForm(CsvFormType::class, $csvForm);
        $csvRegisterForm->handleRequest($request);

        // Soumission du formulaire de création d'un seul participant
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword( $passwordEncoder->encodePassword( $user, "password" ) );
            // Choisir si les comptes sont actifs ou inactifs lors de la création
            $user->setCreatedDate(new \DateTime());
            $user->setRoles([$form->get("roles")->getData()]);
            $entityManager->persist($user);
            $entityManager->flush();
            // Message flash d'aide à l'utilisateur
            $this->addFlash("link", "L'utilisateur a été créée");
            return $this->redirectToRoute('app_register');
        }

        // Soumission du formulaire de création d'un seul participant
        if ( $csvRegisterForm->isSubmitted() && $csvRegisterForm->isValid() ) {
            // On charge le fichier csv dans notre répertoire
            $isItUploaded = $importParticipants->uploadCsvFile($csvRegisterForm);
            // on lit le fichier s'il est uploadé
            if ($isItUploaded) {
                // on insert toutes les données en base
                $data = $importParticipants->insertParticipantsFromCsvFile();
                // on efface le fichier
                $importParticipants->deleteCsvFile();
                // on traite les résultats des insertions
                if ($data['errorInsert'] === "" && count($data['errorParticipants']) === 0) {
                    $this->addFlash("info", "Les " . $data['nbInsert'] . " participants ont été importé avec succès");
                } else {
                    $nbInsertDone = $data['nbInsert'] - $data['nbErrors'];
                    $this->addFlash("info", $nbInsertDone . " participant(es) d'inséré(es) - Erreur sur le(s) participant(es) : " . implode(' | ', $data['errorParticipants']));
                }
            } else {
                $this->addFlash("danger", "Le téléchargement du fichier a échoué.");
            }
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'csvForm' => $csvRegisterForm->createView(),
        ]);
    }
}
