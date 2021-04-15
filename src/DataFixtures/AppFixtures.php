<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Services\NameState;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

/**
 * Class AppFixtures.
 * Classe permettant la création de jeux de données aléatoires dans chacunes des tables du projet "sortir.com" dans la base de données MySql.
 * @package App\DataFixtures
 */
class AppFixtures extends Fixture
{


    // Constantes :
    // Mot de passe des Fixtures
    const PLAIN_PASSWORD = "azerty";

    // Les états des sorties :
    const STATES_START = [NameState::STATE_CREATED, NameState::STATE_OPEN, NameState::STATE_END_REGISTER, NameState::STATE_CANCELED, NameState::STATE_HISTORIZED];
    const STATES_END = [NameState::STATE_IN_PROGRESS, NameState::STATE_DONE];

    // la liste des campus ENI :
    const CAMPUS_LIST = ["st herblain", "chartres de bretagne", "niort", "la roche sur yon", "angers", "quimper", "le mans", "laval"];

    // Les roles des utilisateurs :
    const ROLES = [["ROLE_USER"],["ROLE_ADMIN"]];




    /**
     * Méthode de chargement des jeux de données dans chacune des tables du projet "sortir.com".
     * Les tables concernées sont : Participant, Sortie, Campus, Etat, Lieu, et Ville.
     * @note cmd : "php bin/console doctrine:fixture:load"
     * @param ObjectManager $manager - ObjectManager de la couche d'accès aux données (via l'ORM Doctrine).
     */
    public function load(ObjectManager $manager)
    {
        // Utilisation du Bundle FakerPhp
        $faker = Factory::create("fr_FR");

        // Jeu de données Ville :
        for ($i = 0; $i < 50; $i++) {
            $city = new Ville();
            $city->setName($faker->city);
            // on créer des codes postales aléatoirement (La Corse et les DOM-TOM ne sont pas compris)
            $postCode = $faker->numberBetween(100,9599)*10;
            if ($postCode < 10000){
                $postCode = 0 . $postCode;
            }
            $city->setPostcode($postCode);
            $manager->persist($city);
        }
        $manager->flush();

        // Jeu de données Etat (Début):
        foreach (self::STATES_START as $state) {
            $stateDb = new Etat();
            $stateDb->setWording($state);
            $manager->persist($stateDb);
        }
        $manager->flush();

        // Jeu de données Lieu :
        $cities = $manager->getRepository(Ville::class)->findAll();
        for ($i = 0; $i < 100; $i++) {
            $location = new Lieu();
            $location->setName($faker->company);
            $location->setStreet($faker->streetAddress);
            $location->setLatitude($faker->latitude);
            $location->setLongitude($faker->longitude);
            $location->setCity($faker->randomElement($cities));
            $manager->persist($location);
        }
        $manager->flush();

        // Jeu de données Campus :
        foreach (self::CAMPUS_LIST as $campus) {
            $campusDb = new Campus();
            $campusDb->setName($campus);
            $manager->persist($campusDb);
        }
        $manager->flush();

        // Jeu de données Participants :
        $campuses = $manager->getRepository(Campus::class)->findAll();
        for ($i = 0; $i < 200; $i++) {
            // Creation du mot de passe hashé
            $pwd = password_hash(self::PLAIN_PASSWORD, PASSWORD_BCRYPT);
            // On hydrate le nouveau participant
            $user = new Participant();
            $user->setName($faker->name);
            $user->setUsername($faker->userName);
            $user->setFirstname($faker->firstName);
            $user->setPhone($faker->phoneNumber);
            $user->setEmail($faker->email);
            $idx = $faker->optional($weight = 10, $default = 0)->numberBetween(1,1);
            $user->setRoles(self::ROLES[$idx]);
            $user->setIsActive($faker->boolean(90));
            $user->setPassword($pwd);
            $user->setCreatedDate($faker->dateTimeBetween('-1 years'));
            $user->setCampus($faker->randomElement($campuses));
            $manager->persist($user);
        }
        $manager->flush();

        // Jeu de données Sorties :
        $participants = $manager->getRepository(Participant::class)->findAll();
        $locations = $manager->getRepository(Lieu::class)->findAll();
        $statesDb = $manager->getRepository(Etat::class)->findAll();
        for ($i = 0; $i < 200; $i++) {
            $event = new Sortie();
            $event->setOrganizer($faker->randomElement($participants));
            // On ajoute un nombre aléatoire de participants à la sortie
            for ($j = 0; $j < $faker->numberBetween(0, 5); $j++) {
                $event->addParticipant($faker->randomElement($participants));
            }
            $event->setOrganizingSite($faker->randomElement($campuses));
            $event->setLocation($faker->randomElement($locations));
            $event->setState($faker->randomElement($statesDb));
            $event->setStartDate($faker->dateTimeBetween('-10 days', '10 days'));
            $event->setDeadLine($faker->dateTimeBetween('-10 days', $event->getStartDate()));
            // Duration : Exprimée en 'minutes' :
            $event->setDuration($faker->numberBetween(30, 720));
            $event->setMaxRegistrations(5);
            $event->setDescription($faker->realTextBetween(30, 500));
            $event->setName($faker->sentence);
            $manager->persist($event);
        }
        $manager->flush();

        // Jeu de données Etat (Fin) :
        foreach (self::STATES_END as $state) {
            $stateDb = new Etat();
            $stateDb->setWording($state);
            $manager->persist($stateDb);
        }
        $manager->flush();

    }
}
