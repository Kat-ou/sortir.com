<?php


namespace App\Services;


use App\Entity\Sortie;

class EventManagement
{


    /**
     * Méthode permettant de vérifier si un utilisateur passé en paramètre
     * est dans la liste des participants de l'instance "Sortie".
     * @param $user - L'utilisateur faisant l'objet de la vérification.
     * @param $event - La sortie concernée par la vérification.
     * @return bool True s'il participe à la sortie, sinon False.
     */
    public function isItParticipantOfEvent($user, Sortie $event): bool
    {
        $result = false;
        foreach ($event->getParticipants() as $participant) {
            if ($participant->getId() == $user->getId()) {
                $result = true;
            }
        }
        return $result;
    }


    /**
     * Méthode permettant de vérifier si un utilisateur passé en paramètre
     * peut modifier ou publier une sortie.
     * @param $user - Le participant connecté.
     * @param $event - La sortie concernée par la vérification.
     * @return bool - True s'il peut, sinon False.
     */
    public function isItPossibleToModifyOrPublish($user, Sortie $event): bool
    {
        $result = false;
        if ($event->getState()->getWording() == NameState::STATE_CREATED && $event->getOrganizer()->getId() == $user->getId() ) {
            $result = true;
        }
        return $result;
    }


    /**
     * Méthode permettant de vérifier si un utilisateur passé en paramètre
     * peut s'inscrire à une sortie.
     * @param $user - Le participant connecté.
     * @param $event - La sortie concernée par la vérification.
     * @return bool - True s'il peut, sinon False.
     */
    public function isItPossibleToRegister($user, Sortie $event): bool
    {
        $result = false;
        $today = new \DateTime('now');
        if ($event->getState()->getWording() == NameState::STATE_OPEN &&
            !$this->isItParticipantOfEvent($user, $event) &&
            count($event->getParticipants()) < $event->getMaxRegistrations() &&
            $today < $event->getDeadLine()) {
            $result = true;
        }
        return $result;
    }


    /**
     * Méthode permettant de vérifier si un utilisateur passé en paramètre
     * peut se désister d'une sortie.
     * @param $user - Le participant connecté.
     * @param $event - La sortie concernée par la vérification.
     * @return bool - True s'il peut, sinon False.
     */
    public function isItPossibleToRenounce($user, Sortie $event): bool
    {
        $result = false;
        $eventStatement = $event->getState()->getWording();
        $today = new \DateTime('now');
        if ( $this->isItParticipantOfEvent($user, $event) &&
            ( $eventStatement == NameState::STATE_OPEN || ( $eventStatement == NameState::STATE_END_REGISTER && $today < $event->getDeadLine() ) ) ) {
            $result = true;
        }
        return $result;
    }


    /**
     * Méthode permettant de vérifier si un utilisateur passé en paramètre
     * peut annuler une sortie.
     * @param $user - Le participant connecté.
     * @param $event - La sortie concernée par la vérification.
     * @return bool - True s'il peut, sinon False.
     */
    public function isItPossibleToCancel($user, Sortie $event): bool
    {
        $result = false;
        $eventStatement = $event->getState()->getWording();
        if ( ($eventStatement == NameState::STATE_OPEN || $eventStatement == NameState::STATE_END_REGISTER) &&
            $event->getOrganizer()->getId() == $user->getId() ) {
            $result = true;
        }
        return $result;
    }


    /**
     * Méthode permettant de vérifier si un utilisateur peut afficher les détails d'une sortie.
     * @param $event - La sortie concernée par la vérification.
     * @return bool - True s'il peut, sinon False.
     */
    public function isItPossibleToDisplay(Sortie $event)
    {
        return ($event->getState()->getWording() != NameState::STATE_CREATED);
    }




}