<?php

namespace App\Entity;

use App\Repository\SortieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SortieRepository::class)
 */
class Sortie
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="datetime")
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $deadLine;

    /**
     * @ORM\Column(type="integer")
     */
    private $duration;

    /**
     * @ORM\Column(type="integer")
     */
    private $maxRegistrations;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=Participant::class, inversedBy="events")
     * @ORM\JoinColumn(nullable=false)
     */
    private $organizer;

    /**
     * @ORM\ManyToMany(targetEntity=Participant::class, inversedBy="eventRegistrationList")
     */
    private $participants;

    /**
     * @ORM\ManyToOne(targetEntity=Campus::class, inversedBy="eventsOrganizingSite")
     * @ORM\JoinColumn(nullable=false)
     */
    private $organizingSite;

    /**
     * @ORM\ManyToOne(targetEntity=Lieu::class, inversedBy="eventsLocation")
     * @ORM\JoinColumn(nullable=false)
     */
    private $location;

    /**
     * @ORM\ManyToOne(targetEntity=Etat::class, inversedBy="eventsExistingList")
     * @ORM\JoinColumn(nullable=false)
     */
    private $state;




    /**
     * Sortie constructor.
     */
    public function __construct()
    {
        $this->participants = new ArrayCollection();
    }




    /**
     * Méthode permettant de vérifier si un utilisateur passé en paramètre
     * est dans la liste des participants de l'instance "Sortie".
     * @param $user L'utilisateur faisant l'objet de la vérification.
     * @return bool True s'il participe à la sortie, sinon False.
     */
    public function isItParticipantOfEvent($user): bool
    {
        $result = false;
        foreach ($this->participants as $participant) {
            if ($participant->getId() == $user->getId()) {
                $result = true;
            }
        }
        return $result;
    }


    /**
     * @param $user
     * @return bool
     */
    public function isItPossibleToModifyOrPublish($user): bool
    {
        $result = false;
        if ($this->getState()->getWording() == 'Créée' && $this->getOrganizer()->getId() == $user->getId() ) {
            $result = true;
        }
        return $result;
    }


    /**
     * @param $user
     * @return bool
     */
    public function isItPossibleToRegister($user): bool
    {
        $result = false;
        $today = new \DateTime('now');
        if ($this->getState()->getWording() == 'Ouverte' &&
            !$this->isItParticipantOfEvent($user) &&
            count($this->getParticipants()) < $this->getMaxRegistrations() &&
            $today < $this->getDeadLine()) {
            $result = true;
        }
        return $result;
    }


    /**
     * @param $user
     * @return bool
     */
    public function isItPossibleToRenounce($user): bool
    {
        $result = false;
        $eventStatement = $this->getState()->getWording();
        $today = new \DateTime('now');
        if ( $this->isItParticipantOfEvent($user) && ( $eventStatement == 'Ouverte' || ( $eventStatement == 'Clôturée' && $today < $this->deadLine ) ) ) {
            $result = true;
        }
        return $result;
    }


    /**
     * @param $user
     * @return bool
     */
    public function isItPossibleToCancel($user): bool
    {
        $result = false;
        $eventStatement = $this->getState()->getWording();
        if ( ($eventStatement == 'Ouverte' || $eventStatement == 'Clôturée') && $this->getOrganizer()->getId() == $user->getId() ) {
            $result = true;
        }
        return $result;
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getDeadLine(): ?\DateTimeInterface
    {
        return $this->deadLine;
    }

    public function setDeadLine(\DateTimeInterface $deadLine): self
    {
        $this->deadLine = $deadLine;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getMaxRegistrations(): ?int
    {
        return $this->maxRegistrations;
    }

    public function setMaxRegistrations(int $maxRegistrations): self
    {
        $this->maxRegistrations = $maxRegistrations;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getOrganizer(): ?Participant
    {
        return $this->organizer;
    }

    public function setOrganizer(?Participant $organizer): self
    {
        $this->organizer = $organizer;

        return $this;
    }

    /**
     * @return Collection|Participant[]
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(Participant $participant): self
    {
        if (!$this->participants->contains($participant)) {
            $this->participants[] = $participant;
        }

        return $this;
    }

    public function removeParticipant(Participant $participant): self
    {
        $this->participants->removeElement($participant);

        return $this;
    }

    public function getOrganizingSite(): ?Campus
    {
        return $this->organizingSite;
    }

    public function setOrganizingSite(?Campus $organizingSite): self
    {
        $this->organizingSite = $organizingSite;

        return $this;
    }

    public function getLocation(): ?Lieu
    {
        return $this->location;
    }

    public function setLocation(?Lieu $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getState(): ?Etat
    {
        return $this->state;
    }

    public function setState(?Etat $state): self
    {
        $this->state = $state;

        return $this;
    }
}
