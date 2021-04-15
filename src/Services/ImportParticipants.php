<?php


namespace App\Services;


use App\Entity\Participant;
use App\Repository\CampusRepository;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Classe 'ImportParticipants', gérant l'importation en masse de participants via un chargement de fichier .csv
 * @package App\Services
 */
class ImportParticipants
{

    private const FILE_NAME = 'csv_register.csv';

    private $uploadCsvDir;
    private $participantRepository;
    private $campusRepository;
    private $passwordEncoder;
    private $entityManager;


    public function __construct($uploadCsvDir, ParticipantRepository $participantRepository, CampusRepository $campusRepository,
                                UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager)
    {
        $this->uploadCsvDir = $uploadCsvDir;
        $this->participantRepository = $participantRepository;
        $this->campusRepository = $campusRepository;
        $this->passwordEncoder = $passwordEncoder;
        $this->entityManager = $entityManager;
    }


    /**
     * Méthode téléchargeant le fichier dans l'emplacement "/public/files/".
     * Méthode retournant True si le téléchargment s'est correctement déroulé ; sinon False.
     * @param $csvRegisterForm - L'élément formulaire récupéré apres soumission.
     * @return bool - True si le téléchargement a été réalisé avec succes ; sinon False.
     */
    public function uploadCsvFile($csvRegisterForm): bool
    {
        $isItUploaded = true;
        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $csvRegisterForm->get('csvFile')->getData();
        // on génere un nom de fichier générique
        $newFileName = self::FILE_NAME;
        // on déplace le fichier dans le répertoire public avant sa destruction
        try {
            $uploadedFile->move($this->getUploadCsvDir(), $newFileName);
        } catch (\Exception $e) {
            $isItUploaded = false;
        }
        return $isItUploaded;
    }


    /**
     * Méthode gérant l'insertion des nouveaux participants en base de données.
     * Elle retourne les éléments de résultats :
     * Message d'erreur à l'insertion,
     * message d'erreur des participants non insérés,
     * le nombre de ligne traitées,
     * le nombre de lignes non traitées.
     * @return array - Tableau avec les résultats de l'insertion en base.
     */
    public function insertParticipantsFromCsvFile() {
        $fileStr = $this->getUploadCsvDir() . self::FILE_NAME;
        $handle = fopen($fileStr, 'r');
        $i = 0;
        $errorParticipants = [];
        $errorInsert = "";
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $i++;
            $isPersonAlreadyExist = $this->participantRepository->isParticipantAlreadyExist((string) $data[4], (string) $data[0]);
            if (!$isPersonAlreadyExist && $this->isControlDataValidate($data)) {
                $campusParticipant = $this->campusRepository->findOneBy(['name' => (string) $data[9]]);
                $participantToRegister = new Participant();
                $participantToRegister
                    ->setEmail((string) $data[0])
                    ->setRoles([$data[1]])
                    ->setPassword($this->passwordEncoder->encodePassword( $participantToRegister, (string) $data[2] ))
                    ->setName((string) $data[3])
                    ->setUsername((string) $data[4])
                    ->setFirstname((string) $data[5])
                    ->setPhone((string) $data[6])
                    ->setIsActive((bool) $data[7])
                    ->setPictureFilename(null)
                    ->setCampus($campusParticipant)
                    ->setCreatedDate(new \DateTime('now'));
                try {
                    $this->entityManager->persist($participantToRegister);
                    $this->entityManager->flush();
                } catch (\Exception $e) {
                    $errorInsert = "L'import des participants a échoué lors de la ligne n° " . $i . " (Mr/Mme " . (string) $data[3] . " " . (string) $data[5] . ").";
                }
            } else {
                $errorParticipants[] = (string) $data[3] . " " . (string) $data[5];
            }
        }
        return [
            'errorInsert' => $errorInsert,
            'errorParticipants' => $errorParticipants,
            'nbInsert' => $i,
            'nbErrors' => count($errorParticipants),
        ];
    }


    /**
     * Procédure de suppression du fichier CSV dans le répertoire "/public/files/"
     */
    public function deleteCsvFile() {
        unlink($this->getUploadCsvDir() . self::FILE_NAME);
    }


    /**
     * Méthode controllant l'absence de champs vide avant l'insertion en base de données.
     * @param $data - Les données du fichier CSV
     * @return bool - True si les champs nécéssaires sont remplis ; sinon False.
     */
    private function isControlDataValidate($data) {
        $result = true;
        for ($j = 0 ; $j < 10 ; $j++) {
            if ($j !== 6 && $j !== 8 && $data[$j] == "") {
                $result = false;
                break;
            }
        }
        return $result;
    }


    /**
     * Ascesseur de propriété : "$uploadCsvDir" (le répertoire d'accès au fichier CSV)
     * @return mixed
     */
    private function getUploadCsvDir()
    {
        return $this->uploadCsvDir;
    }




}