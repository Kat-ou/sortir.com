<?php

namespace App\Entity;

use App\Repository\LieuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LieuRepository::class)
 */
class Lieu
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $street;

    /**
     * @ORM\Column(type="float")
     */
    private $latitude;

    /**
     * @ORM\Column(type="float")
     */
    private $longitude;

    /**
     * @ORM\ManyToOne(targetEntity=Ville::class, inversedBy="locations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $city;

    /**
     * @ORM\OneToMany(targetEntity=Sortie::class, mappedBy="location")
     */
    private $eventsLocation;

    public function __construct()
    {
        $this->eventsLocation = new ArrayCollection();
    }

    public function toArray()
    {
        return [
            'name' => $this->getName(),
            'street' => $this->getStreet(),
            'latitude' => $this->getLatitude(),
            'longitude' => $this->getLongitude(),
            'postcode' => $this->getCity()->getPostcode()
        ];
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

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getCity(): ?Ville
    {
        return $this->city;
    }

    public function setCity(?Ville $city): self
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return Collection|Sortie[]
     */
    public function getEventsLocation(): Collection
    {
        return $this->eventsLocation;
    }

    public function addEventsLocation(Sortie $eventsLocation): self
    {
        if (!$this->eventsLocation->contains($eventsLocation)) {
            $this->eventsLocation[] = $eventsLocation;
            $eventsLocation->setLocation($this);
        }

        return $this;
    }

    public function removeEventsLocation(Sortie $eventsLocation): self
    {
        if ($this->eventsLocation->removeElement($eventsLocation)) {
            // set the owning side to null (unless already changed)
            if ($eventsLocation->getLocation() === $this) {
                $eventsLocation->setLocation(null);
            }
        }

        return $this;
    }
}
