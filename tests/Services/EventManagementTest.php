<?php

namespace App\Tests\Services;

use App\Entity\Etat;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Services\EventManagement;
use App\Services\NameState;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class EventManagementTest extends KernelTestCase
{

    private Sortie $event_1;
    private Participant $user_1;
    private Participant $user_2;
    private Participant $orga_1;
    private Participant $state_open;

    private EventManagement $eventManagement;
    private $entityManager;


    /**
     * Méthode appeler avant tous les tests :
     */
    protected function setUp(): void
    {
        $this->eventManagement = new EventManagement();

        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->event_1 = $this->entityManager
            ->getRepository(Sortie::class)
            ->find(1703);

        $this->user_1 = $this->entityManager
            ->getRepository(Participant::class)
            ->find(4424);
        $this->user_2 = $this->entityManager
            ->getRepository(Participant::class)
            ->find(4431);

        $this->orga_1 = $this->entityManager
            ->getRepository(Participant::class)
            ->find(4433);

        $this->state_open = $this->entityManager
            ->getRepository(Etat::class)
            ->findOneBy(['wording' => NameState::STATE_OPEN]);

    }


    /**
     * Procédure de test valide de la méthode "isItPossibleToDisplay()"
     */
    public function testItIsPossibleToDisplay(): void
    {
        $result = $this->eventManagement->isItPossibleToDisplay($this->event_1);
        $this->assertTrue($result);
    }


    /**
     * Procédure de test valide de la méthode "isItParticipantOfEvent()"
     */
    public function testHeIsParticipantOfEvent(): void
    {
        $result = $this->eventManagement->isItParticipantOfEvent($this->user_1, $this->event_1);
        $this->assertTrue($result);
    }


    /**
     * Procédure de test non-valide de la méthode "isItParticipantOfEvent()"
     */
    public function testHeIsNotParticipantOfEvent(): void
    {
        $result = $this->eventManagement->isItParticipantOfEvent($this->user_2, $this->event_1);
        $this->assertFalse($result);
    }



}
