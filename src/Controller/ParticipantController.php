<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ProfileFormType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ParticipantController extends AbstractController
{
    /**
     * @Route("/profile/{id}", name="profile")
     */
    public function profile($id, ParticipantRepository $participantRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $participant = $participantRepository->find($id);

        // Crée une instance de l'entité que le form sert à créer
        $profile = new Participant();

        // On récupère des données dans l'instance participant
        $currentUsername = $participant->getUsername();
        $currentFirstName = $participant->getFirstname();
        $currentName = $participant->getName();
        $currentPhone = $participant->getPhone();
        $currentEmail = $participant->getEmail();
        $currentCampus = $participant->getCampus()->getName();

        // Crée une instance de la classe de formulaire que l'on assicie à notre formulaire
        $profileForm = $this->createForm(ProfileFormType::class, $profile);

        // On injecte des données dans le formulaire
        $profileForm->get('username')->setData($currentUsername);
        $profileForm->get('firstname')->setData($currentFirstName);
        $profileForm->get('name')->setData($currentName);
        $profileForm->get('phone')->setData($currentPhone);
        $profileForm->get('email')->setData($currentEmail);
        $profileForm->get('campus')->setData($currentCampus);

        // On prend les données du formulaire soumis, et les injecte dans mon $profil
        $profileForm->handleRequest($request);

        // Si le formulaire est soumis
        if ($profileForm->isSubmitted() && $profileForm->isValid()) {

            // Hydrate les propriétés qui sont encore null
            $participant->setRoles(['ROLES_USER']);


            // Sauvegarde en Bdd
            $entityManager->persist($participant);
            $entityManager->flush();

            // On ajoute un message flash
            $this->addFlash("success", "Le message a été enregistré");
        }

        return $this->render('participant/profile.html.twig', [
            "profileForm" => $profileForm->createView(),
        ]);
    }
}
