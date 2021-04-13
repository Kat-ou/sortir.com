<?php


namespace App\Services;


use App\Entity\Etat;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class RefreshStatesEvents.
 * Service permettant la gestion d'état des sorties présentent en base de données.
 *
 * @package App\Services
 */
class RefreshStatesEvents
{

    // objets d'accès aux données
    private EntityManagerInterface $entityManager;
    private SortieRepository $sortieRepository;
    private EtatRepository $etatRepository;

    // les listes d'états et des sorties à mettre à jour
    private $eventsListToRefresh;
    private $states;


    /**
     * Constructeur de la classe RefreshStatesEvents.
     * @param SortieRepository $sr
     * @param EtatRepository $er
     * @param EntityManagerInterface $em
     */
    public function __construct(SortieRepository $sr, EtatRepository $er, EntityManagerInterface $em)
    {
        $this->entityManager = $em;
        $this->etatRepository = $er;
        $this->sortieRepository = $sr;
        $this->eventsListToRefresh = new ArrayCollection();
    }


    /**
     *  Méthode permettant de mettre à jour les états des sorties en fonction du temps et des inscriptions.
     * Les états des sorties pris en compte sont : "Créée, Ouverte, Clôturée, et Activité en cours".
     * Les états non-pris en compte sont : "Passée, Annulée, et Historisée".
     */
    public function refreshStateEventsIntoDb()
    {
        // On récupère les sorties concernées pour les mises à jour ; ainsi que les états
        $this->states = $this->etatRepository->findAll();
        $eventsListToRefresh = $this->sortieRepository->findEventsBySevralStates();
        // on récupère la date du jour
        $today = new \DateTime('now');

        // on contrôle chaque sorties pour MAJ leurs états
        foreach ($eventsListToRefresh as $event) {
            /** @var \DateTime $startDateEvent */
            $startDateEvent = $event->getStartDate();
            $limitDateRegister = $event->getDeadLine();
            $maxPersonEvent = $event->getMaxRegistrations();
            $countParticipants = count($event->getParticipants());
            // on traite selon l'etat de la sortie :
            switch ($event->getState()->getWording()) {
                // si l'état de la sortie est Ouverte
                case NameState::STATE_OPEN:
                    // si ( le nombre de participants est max OU que aujourd'hui > à la date de cloture )
                    if ( $countParticipants == $maxPersonEvent || $today > $limitDateRegister) {
                        // on passe l'event à l'état -> cloturée
                        $event->setState($this->getStateByName(NameState::STATE_END_REGISTER));
                        $this->entityManager->persist($event);
                    }
                    break;
                // si l'état de la sortie est cloturée
                case NameState::STATE_END_REGISTER:
                    // si ( le nombre de participants < max ET que aujourd'hui <= à la date de cloture )
                    if ($countParticipants < $maxPersonEvent && $today <= $limitDateRegister) {
                        // on passe l'event à l'état -> Ouverte
                        $event->setState($this->getStateByName(NameState::STATE_OPEN));
                        $this->entityManager->persist($event);
                    }
                    // si aujourd'hui > date de début
                    if ($today > $startDateEvent) {
                        // on passe l'event à l'état -> En cours
                        $event->setState($this->getStateByName(NameState::STATE_IN_PROGRESS));
                        $this->entityManager->persist($event);
                    }
                    break;
                // si l'état de la sortie est En Cours
                case NameState::STATE_IN_PROGRESS:
                    // récupération de la durée
                    $durationEvent = $event->getDuration();
                    // création d'une dateTime de fin de sortie avec la durée et la date de début :
                    $date = date_create($startDateEvent->format('Y-m-d H:i:s'));
                    $endDateEvent = date_add($date, date_interval_create_from_date_string("$durationEvent minutes"));
                    // si aujourd'hui > (date de début + durée en min)
                    if ($today > $endDateEvent) {
                        // on passe l'event à l'état -> Terminée
                        $event->setState($this->getStateByName(NameState::STATE_DONE));
                        $this->entityManager->persist($event);
                    }
                    break;
                case NameState::STATE_CREATED:
                    // si la date du jour est supérieure à sa date de cloture
                    if ($today > $limitDateRegister) {
                        // on passe l'event à l'état -> Annulée
                        $event->setState($this->getStateByName(NameState::STATE_CANCELED));
                        $this->entityManager->persist($event);
                    }
                    break;
                // si l'état de la sortie est Annulée, Historisée, passée ou autres
                // au cas ou (car la requete ne les récupère normalement pas)
                case NameState::STATE_DONE:
                case NameState::STATE_CANCELED:
                case NameState::STATE_HISTORIZED:
                default:
                    break;
            }
        }
        // on valide nos changements en base
        $this->entityManager->flush();
    }


    /**
     * Méthode permettant de retourner l'objet Etat en fonction du nom d'état passée en paramètre.
     * @param $name - Le nom de l'état souhaité.
     * @return Etat - l'objet Etat correspondant.
     */
    private function getStateByName($name)
    {
        $result = null;
        foreach ($this->states as $state) {
            if ($state->getWording() == $name) {
                $result = $state;
                break;
            }
        }
        return $result;
    }







}