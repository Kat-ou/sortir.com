<?php

namespace App\Controller;

use App\Form\ProfileFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\String\ByteString;

class ParticipantController extends AbstractController
{
    /**
     * @Route("/profile", name="profile")
     */
    public function profile(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder): Response
    {
        $user = $this->getUser();

        // Crée une instance de la classe de formulaire que l'on assicie à notre formulaire
        $profileForm = $this->createForm(ProfileFormType::class, $user);

        // On prend les données du formulaire soumis, et les injecte dans mon $profil
        $profileForm->handleRequest($request);

        // Si le formulaire est soumis
        if ($profileForm->isSubmitted() && $profileForm->isValid()) {

            $uploadedFile = $profileForm->get('pictureFilename')->getData();
            if ($uploadedFile != "") {
                //génère un nom de fichier sécuritaire
                $newFilename = ByteString::fromRandom(30) . "." . $uploadedFile->guessExtension();
                //déplace le fichier dans mon répertoire public/ avant sa destruction
                //upload_dir est défini dans config/services.yaml
                try {
                    $uploadedFile->move($this->getParameter('upload_dir'), $newFilename);
                } catch (\Exception $e){
                    dd($e->getMessage());
                }
                //détruit l'originale
                //unlink($this->getParameter('upload_dir') . "/$newFilename");
                $user->setPictureFilename($newFilename);
            }

            $user->setUpdatedDate(new \DateTime());

            // Sauvegarde en Bdd
            $entityManager->persist($user);
            $entityManager->flush();

            // On ajoute un message flash
            $this->addFlash("success", "Votre profil a été modifié");
        }

        return $this->render('participant/profile.html.twig', [
            "profileForm" => $profileForm->createView(),
        ]);
    }
}
