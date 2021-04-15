<?php

namespace App\Tests\Services;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Services\EventManagement;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class EventManagementTest extends KernelTestCase
{

    private Sortie $event_1;        // Ouverte
    private Sortie $event_2;        // Creee
    private Participant $user_1;    // e1
    private Participant $user_2;
    private Participant $orga_1;    // e1
    private Participant $orga_2;    // e2

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
        $this->event_2 = $this->entityManager
            ->getRepository(Sortie::class)
            ->find(1879);

        $this->user_1 = $this->entityManager
            ->getRepository(Participant::class)
            ->find(4424);
        $this->user_2 = $this->entityManager
            ->getRepository(Participant::class)
            ->find(4431);

        $this->orga_1 = $this->entityManager
            ->getRepository(Participant::class)
            ->find(4433);
        $this->orga_2 = $this->entityManager
            ->getRepository(Participant::class)
            ->find(4558);

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
     * Procédure de test non-valide de la méthode "isItPossibleToDisplay()"
     */
    public function testItIsNotPossibleToDisplay(): void
    {
        $result = $this->eventManagement->isItPossibleToDisplay($this->event_2);
        $this->assertFalse($result);
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

    /**
     * Procédure de test valide de la méthode "isItPossibleToRenounce()"
     */
    public function testItIsPossibleToRenounce(): void
    {
        $result = $this->eventManagement->isItPossibleToRenounce($this->user_1, $this->event_1);
        $this->assertTrue($result);
    }

    /**
     * Procédure de test valide de la méthode "isItPossibleToRegister()"
     */
    public function testItIsPossibleToRegister(): void
    {
        $result = $this->eventManagement->isItPossibleToRegister($this->user_2, $this->event_1);
        $this->assertTrue($result);
    }

    /**
     * Procédure de test valide de la méthode "isItPossibleToModifyOrPublish()"
     */
    public function testItIsPossibleToModifyOrPublish(): void
    {
        $result = $this->eventManagement->isItPossibleToModifyOrPublish($this->orga_2, $this->event_2);
        $this->assertTrue($result);
    }

    /**
     * Procédure de test valide de la méthode "isItPossibleToCancel()"
     */
    public function testItIsPossibleToCancel(): void
    {
        $result = $this->eventManagement->isItPossibleToCancel($this->orga_1, $this->event_1);
        $this->assertTrue($result);
    }

    /**
     * Procédure de test valide de la méthode "isItPossibleToDelete()"
     */
    public function testItIsPossibleToDelete(): void
    {
        $result = $this->eventManagement->isItPossibleToDelete($this->orga_2, $this->event_2);
        $this->assertTrue($result);
    }




}
