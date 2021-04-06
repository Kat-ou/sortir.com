<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create("fr_FR");
        $states = ["Créée", "Ouverte", "Clôturée", "Activité en cours", "Passée", "Annulée"];
        $campusList = ["st herblain", "chartres de bretagne", "niort", "la roche sur yon", "angers", "quimper", "le mans", "laval"];
        $roles = ["ROLE_USER"];

        // Jeu de données Ville :
        for ($i = 0; $i < 50; $i++) {
            $city = new Ville();
            $city->setName($faker->city);
            $postCode = $faker->numberBetween(100,9599)*10;
            if ($postCode < 10000){
                $postCode = 0 . $postCode;
            }
            $city->setPostcode($postCode);
            $manager->persist($city);
        }
        $manager->flush();

        // Jeu de données Etat :
        foreach ($states as $state) {
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
        foreach ($campusList as $campus) {
            $campusDb = new Campus();
            $campusDb->setName($campus);
            $manager->persist($campusDb);
        }
        $manager->flush();

        // Jeu de données Participants :
        $campuses = $manager->getRepository(Campus::class)->findAll();
        for ($i = 0; $i < 100; $i++) {
            $user = new Participant();
            $user->setName($faker->name);
            $user->setUsername($faker->userName);
            $user->setFirstname($faker->firstName);
            $user->setPhone($faker->phoneNumber);
            $user->setEmail($faker->email);
            $user->setRoles($faker->randomElements($roles));
            $user->setIsActive($faker->boolean(90));
            $pwd = password_hash("azerty", PASSWORD_BCRYPT);
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
        for ($i = 0; $i < 25; $i++) {
            $event = new Sortie();
            $event->setOrganizer($faker->randomElement($participants));
            for ($j = 0; $j < $faker->numberBetween(0, 10); $j++) {
                $event->addParticipant($faker->randomElement($participants));
            }
            $event->setOrganizingSite($faker->randomElement($campuses));
            $event->setLocation($faker->randomElement($locations));
            $event->setState($faker->randomElement($statesDb));
            $event->setStartDate($faker->dateTimeBetween('-20 days', 'now'));
            $event->setDeadLine($faker->dateTimeBetween('-1 month', $event->getStartDate()));
            $event->setDuration($faker->numberBetween(30, 600));                                // minutes
            $event->setMaxRegistrations(10);
            $event->setDescription($faker->realTextBetween(30, 1500));
            $event->setName($faker->sentence);
            $manager->persist($event);
        }
        $manager->flush();

    }
}
