<?php

namespace App\Entity;

use App\Repository\SortieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

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
     * @Assert\NotBlank(message="Ce champs ne peut pas être vide")
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank(message="Ce champs ne peut pas être vide")
     * @Assert\GreaterThan("+1 day UTC", message="Sélectionnez une date future")
     */
    private $startDate;

    /**
     * @Assert\NotBlank(message="Ce champs ne peut pas être vide")
     * @Assert\GreaterThan("today UTC", message="Sélectionnez une date future")
     * @ORM\Column(type="datetime")
     */
    private $deadLine;

    /**
     * @Assert\NotBlank(message="Ce champs ne peut pas être vide")
     * @Assert\Range (min=1, minMessage="La durée minimum doit être supérieur à 0")
     * @ORM\Column(type="integer")
     */
    private $duration;

    /**
     * @Assert\NotBlank(message="Ce champs ne peut pas être vide")
     * @Assert\Range(min=1, minMessage="Le nombre minimum de personne doit être supérieur à 0")
     * @ORM\Column(type="integer")
     */
    private $maxRegistrations;

    /**
     * @Assert\NotBlank(message="Ce champs ne peut pas être vide")
     * @Assert\Length (max=500, maxMessage="La description ne doit pas dépasser 500 caractères")
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
     * @Assert\NotBlank (message="Ce champs ne peut pas être vide")
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
     * @Assert\Callback
     */
    public function validateDates(ExecutionContextInterface $context)
    {
        // Si la date de début de sortie est inférieure à la date de clôture :
        if ($this->getStartDate() < $this->getDeadLine()) {
            // on leve une violation de contrainte sur la propriété DeadLine :
            $context->buildViolation('La date de clôture doit être avant celle du début de la sortie')
                ->atPath('deadLine')
                ->addViolation();
        }
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName($name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate($startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getDeadLine(): ?\DateTimeInterface
    {
        return $this->deadLine;
    }

    public function setDeadLine($deadLine): self
    {
        $this->deadLine = $deadLine;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration($duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getMaxRegistrations(): ?int
    {
        return $this->maxRegistrations;
    }

    public function setMaxRegistrations($maxRegistrations): self
    {
        $this->maxRegistrations = $maxRegistrations;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription($description): self
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
