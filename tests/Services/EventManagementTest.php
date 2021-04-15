<?php

namespace App\Tests\Services;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Services\EventManagement;
use App\Services\NameState;
use PHPUnit\Framework\TestCase;

class EventManagementTest extends TestCase
{


    /**
     * Procédure de test valide de la méthode "isItPossibleToDisplay()"
     */
    public function testItIsPossibleToDisplay(): void
    {
        $eventManagement = new EventManagement();

        $state = new Etat();
        $state->setWording(NameState::STATE_OPEN);
        $event = new Sortie();
        $event->setState($state);

        $result = $eventManagement->isItPossibleToDisplay($event);
        $this->assertTrue($result);
    }







}
