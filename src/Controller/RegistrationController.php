<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\CsvFormType;
use App\Form\ParticipantFormType;
use App\Form\RegistrationFormType;
use App\Model\CsvForm;
use App\Repository\CampusRepository;
use App\Repository\ParticipantRepository;
use App\Security\AppAuthenticator;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\String\ByteString;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/admin/register", name="app_register")
     */
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder, ParticipantRepository $participantRepository,
                             GuardAuthenticatorHandler $guardHandler, AppAuthenticator $authenticator, CampusRepository $campusRepository): Response
    {
        $user = new Participant();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        $csvForm =  new CsvForm();
        $csvRegisterForm = $this->createForm(CsvFormType::class, $csvForm);
        $csvRegisterForm->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password

            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    "password"
                )
            );

            // Choisir si les comptes sont actifs ou inactifs lors de la création

            $user->setCreatedDate(new \DateTime());
            $user->setRoles([$form->get("roles")->getData()]);

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

           return $this->redirectToRoute('app_register');
        }

        if ( $csvRegisterForm->isSubmitted() && $csvRegisterForm->isValid() ) {
            $isItUploaded = true;
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $csvRegisterForm->get('csvFile')->getData();
            dump($uploadedFile->getClientOriginalExtension());
            // on génere un nom de fichier sécu
            $newFileName = 'csv_register.csv';

            // on déplace le fichier dans le répertoire public avant sa destruction
            try {
                $uploadedFile->move($this->getParameter('upload_csv_dir'), $newFileName);
            } catch (\Exception $e) {
                $isItUploaded = false;
                $this->addFlash("danger", "Le téléchargement du fichier a échoué.");
            }
            // on lit le fichier s'il est uploadé
            if ($isItUploaded) {
                $fileStr = $this->getParameter('upload_csv_dir') . 'csv_register.csv';
                $handle = fopen($fileStr, 'r');
                $i = 0;
                $errorParticipants = [];
                $errorInsert = "";
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $i++;
                    $isPersonAlreadyExist = $participantRepository->isParticipantAlreadyExist((string) $data[4], (string) $data[0]);
                    if (!$isPersonAlreadyExist) {
                        $campusParticipant = $campusRepository->findOneBy(['name' => (string) $data[9]]);
                        $participantToRegister = new Participant();
                        $participantToRegister
                            ->setEmail((string) $data[0])
                            ->setRoles([$data[1]])
                            ->setPassword($passwordEncoder->encodePassword( $user, (string) $data[2] ))
                            ->setName((string) $data[3])
                            ->setUsername((string) $data[4])
                            ->setFirstname((string) $data[5])
                            ->setPhone((string) $data[6])
                            ->setIsActive((bool) $data[7])
                            ->setPictureFilename(null)
                            ->setCampus($campusParticipant)
                            ->setCreatedDate(new \DateTime('now'));
                        try {
                            $entityManager->persist($participantToRegister);
                            $entityManager->flush();
                        } catch (\Exception $e) {
                            $errorInsert = "L'import des participants a échoué lors de la ligne n° " . $i . " (Mr/Mme " . (string) $data[3] . " " . (string) $data[5] . ").";
                            $this->addFlash("info", $errorInsert);
                        }
                    } else {
                        $errorParticipants[] = (string) $data[3] . " " . (string) $data[5];
                    }
                }
                // on efface le fichier
                unlink($fileStr);

                if ($errorInsert === "" && $errorParticipants === "") {
                    $this->addFlash("info", "Les " . $i . " participants ont été importé avec succès");
                } else {
                    $this->addFlash("info", ($i - count($errorParticipants) ) . " participant(es) d'inséré(es) - Erreur sur le(s) participant(es) : " . implode(' | ', $errorParticipants));
                }

            }

        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'csvForm' => $csvRegisterForm->createView(),
        ]);
    }
}
