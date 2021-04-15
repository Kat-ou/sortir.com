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
 * @Route("/manage")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_index", methods={"GET"})
     */
    public function index(ParticipantRepository $participantRepository): Response
    {
        return $this->render('admin/index.html.twig', [
            'participants' => $participantRepository->findAll(),
        ]);
    }

    // non utilisé - voir Registration Controller

    /**
     * @Route("/admin/new", name="admin_new", methods={"GET","POST"})
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
     * @Route("/admin/{id}", name="admin_show", methods={"GET"})
     */
    public function show(Participant $participant): Response
    {
        return $this->render('admin/show.html.twig', [
            'participant' => $participant,
        ]);
    }

    /**
     * @Route("/admin/{id}/edit", name="admin_edit", methods={"GET","POST"})
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
     * @Route("/admin/{id}", name="admin_delete", methods={"POST"})
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
}
