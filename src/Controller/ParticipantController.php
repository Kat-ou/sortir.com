<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\ProfileFormType;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use App\Services\PictureServices;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ParticipantController extends AbstractController
{
    /**
     * @Route("/profile", name="participant_profile")
     */
    public function profile(PictureServices $pictureServices,Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder): Response
    {
        $user = $this->getUser();

        // Crée une instance de la classe de formulaire que l'on assicie à notre formulaire
        $profileForm = $this->createForm(ProfileFormType::class, $user);

        // On prend les données du formulaire soumis, et les injecte dans mon $profil
        $profileForm->handleRequest($request);

        // Si le formulaire est soumis
        if ($profileForm->isSubmitted() && $profileForm->isValid()) {

            $uploadedFile = $profileForm->get('pictureFilename')->getData();
            if ($uploadedFile) {
                $pictureFileName = $pictureServices->upload($uploadedFile);
                $user->setPictureFilename($pictureFileName);
            }

            // On récupère le mot de passe en 'claire' et on le hash
            $newPassword = $profileForm->get('password')->getData();
            if ($newPassword != "") {
                $encoded = $encoder->encodePassword($user, $newPassword);
                $user->setPassword($encoded);
            }

            $user->setUpdatedDate(new \DateTime());

            // Sauvegarde en Bdd
            $entityManager->persist($user);
            $entityManager->flush();

            // On ajoute un message flash
            $this->addFlash("link", "Votre profil a été modifié");
        }

        return $this->render('participant/profile.html.twig', [
            "profileForm" => $profileForm->createView(),
        ]);
    }

    /**
     * @Route("/profile/deleted", name="participant_profile_deleted")
     */
    public function profilePicture(PictureServices $pictureServices, EntityManagerInterface $entityManager): Response
    {
        $currentPictureName = $this->getUser()->getPictureFilename();
        if ($currentPictureName != null) {
            $user = $this->getUser();
            $pictureServices->deletePhoto($currentPictureName);
            $user->setPictureFilename(null);
            $user->setUpdatedDate(new \DateTime());
            // Sauvegarde en Bdd
            $entityManager->persist($user);
            $entityManager->flush();
            // On ajoute un message flash
            $this->addFlash("link", "Votre profil a été modifié");
            return $this->redirectToRoute('participant_profile');
        } else {
            $this->addFlash("danger", "Vous n'avez pas de photo de profil. Suppression impossible");
            return $this->redirectToRoute('participant_profile');
        }
    }

    /**
     * @Route("/profil/voir/{eventId}/{participantId}", name="profile_view")
     */
    public function view(int $eventId, int $participantId, ParticipantRepository $participantRepository, SortieRepository $sortieRepository)
    {
        $foundParticipant = $participantRepository->find($participantId);
        if (!$foundParticipant) {
            throw $this->createNotFoundException("Ce profil n'existe pas !");
        }
        $event = $sortieRepository->findAllElementsByEvent($eventId);

        /**
         * @var Sortie $event
         */
        if ($event->getParticipants()->contains($foundParticipant) && $event->getParticipants()->contains($this->getUser())) {
            return $this->render('participant/profileView.html.twig', [
                'foundParticipant' => $foundParticipant,
                'event' => $event
            ]);
        } else {
            // On ajoute un message flash
            $this->addFlash("link", "Vous devez participer à cette sortie pour contacter les autres membres inscrits");
            return $this->redirectToRoute('details', ["id" => $eventId]
            );
        }
    }
}
