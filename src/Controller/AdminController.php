<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantType;
use App\Form\ProfileFormType;
use App\Repository\ParticipantRepository;
use App\Services\PictureServices;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/users")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="users_index", methods={"GET"})
     */
    public function index(ParticipantRepository $participantRepository): Response
    {
        return $this->render('users/index.html.twig', [
            'participants' => $participantRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="users_new", methods={"GET","POST"})
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

            return $this->redirectToRoute('users_index');
        }

        return $this->render('users/new.html.twig', [
            'participant' => $participant,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="users_show", methods={"GET"})
     */
    public function show(Participant $participant): Response
    {
        return $this->render('users/show.html.twig', [
            'participant' => $participant,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="users_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Participant $participant): Response
    {

        $form = $this->createForm(ParticipantType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('users_index');
        }

        return $this->render('users/edit.html.twig', [
            'participant' => $participant,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="users_delete", methods={"POST"})
     */
    public function delete(Request $request, Participant $participant): Response
    {
        if ($this->isCsrfTokenValid('delete'.$participant->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($participant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('users_index');
    }
}
