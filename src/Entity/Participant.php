<?php

namespace App\Entity;

use App\Repository\ParticipantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ParticipantRepository::class)
 * @UniqueEntity(fields={"email"}, message="Cet email est utilisé par un autre compte")
 * @UniqueEntity(fields={"username"}, message="Ce pseudo est utilisé par un autre compte")
 */
class Participant implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\Email(message="Cet email n'est pas valide !")
     * @Assert\NotBlank(message="Ce champ ne doit pas être vide.")
     * @Assert\Length(max=180, maxMessage="Ce champ a un maximum de 180 caractères")
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(message="Ce champ ne doit pas être vide.")
     * @Assert\Length(max=50, maxMessage="Ce champ a un maximum de 50 caractères")
     */
    private $name;

    /**
     * @Assert\NotBlank(message="Ce champ ne doit pas être vide.")
     * @ORM\Column(type="string", length=50, unique=true)
     * @Assert\Length(max=50, maxMessage="Ce champ a un maximum de 50 caractères")
     */
    private $username;

    /**
     * @Assert\NotBlank(message="Ce champ ne doit pas être vide.")
     * @ORM\Column(type="string", length=50)
     * @Assert\Length(max=50, maxMessage="Ce champ a un maximum de 50 caractères")
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=25, nullable=true)
     * @Assert\Length(max=25, maxMessage="Ce champ a un maximum de 25 caractères")
     */
    private $phone;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pictureFilename;

    /**
     * @ORM\ManyToOne(targetEntity=Campus::class, inversedBy="participants")
     * @ORM\JoinColumn(nullable=false)
     */
    private $campus;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedDate;

    /**
     * @ORM\OneToMany(targetEntity=Sortie::class, mappedBy="organizer", cascade={"remove"})
     */
    private $events;

    /**
     * @ORM\ManyToMany(targetEntity=Sortie::class, mappedBy="participants")
     */
    private $eventRegistrationList;

    public function __construct()
    {
        $this->events = new ArrayCollection();
        $this->eventRegistrationList = new ArrayCollection();
    }
    



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail($email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword($password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function setUsername($username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname($firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getPictureFilename(): ?string
    {
        return $this->pictureFilename;
    }

    public function setPictureFilename(?string $pictureFilename): self
    {
        $this->pictureFilename = $pictureFilename;

        return $this;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): self
    {
        $this->campus = $campus;

        return $this;
    }

    public function getCreatedDate(): ?\DateTimeInterface
    {
        return $this->createdDate;
    }

    public function setCreatedDate(\DateTimeInterface $createdDate): self
    {
        $this->createdDate = $createdDate;

        return $this;
    }

    public function getUpdatedDate(): ?\DateTimeInterface
    {
        return $this->updatedDate;
    }

    public function setUpdatedDate(?\DateTimeInterface $updatedDate): self
    {
        $this->updatedDate = $updatedDate;

        return $this;
    }

    /**
     * @return Collection|Sortie[]
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Sortie $event): self
    {
        if (!$this->events->contains($event)) {
            $this->events[] = $event;
            $event->setOrganizer($this);
        }

        return $this;
    }

    public function removeEvent(Sortie $event): self
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getOrganizer() === $this) {
                $event->setOrganizer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Sortie[]
     */
    public function getEventRegistrationList(): Collection
    {
        return $this->eventRegistrationList;
    }

    public function addEventRegistrationList(Sortie $eventRegistrationList): self
    {
        if (!$this->eventRegistrationList->contains($eventRegistrationList)) {
            $this->eventRegistrationList[] = $eventRegistrationList;
            $eventRegistrationList->addParticipant($this);
        }

        return $this;
    }

    public function removeEventRegistrationList(Sortie $eventRegistrationList): self
    {
        if ($this->eventRegistrationList->removeElement($eventRegistrationList)) {
            $eventRegistrationList->removeParticipant($this);
        }

        return $this;
    }
}
