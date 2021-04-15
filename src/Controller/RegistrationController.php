<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\CsvFormType;
use App\Form\RegistrationFormType;
use App\Model\CsvForm;
use App\Repository\CampusRepository;
use App\Repository\ParticipantRepository;
use App\Security\AppAuthenticator;
use App\Services\ImportParticipants;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class RegistrationController extends AbstractController
{
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
