<?php

namespace App\Entity;

use App\Repository\CampusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CampusRepository::class)
 */
class Campus
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
     * @ORM\OneToMany(targetEntity=Participant::class, mappedBy="campus")
     */
    private $participants;

    /**
     * @ORM\OneToMany(targetEntity=Sortie::class, mappedBy="organizingSite")
     */
    private $eventsOrganizingSite;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
        $this->eventsOrganizingSite = new ArrayCollection();
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
            $participant->setCampus($this);
        }

        return $this;
    }

    public function removeParticipant(Participant $participant): self
    {
        if ($this->participants->removeElement($participant)) {
            // set the owning side to null (unless already changed)
            if ($participant->getCampus() === $this) {
                $participant->setCampus(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Sortie[]
     */
    public function getEventsOrganizingSite(): Collection
    {
        return $this->eventsOrganizingSite;
    }

    public function addEventsOrganizingSite(Sortie $eventsOrganizingSite): self
    {
        if (!$this->eventsOrganizingSite->contains($eventsOrganizingSite)) {
            $this->eventsOrganizingSite[] = $eventsOrganizingSite;
            $eventsOrganizingSite->setOrganizingSite($this);
        }

        return $this;
    }

    public function removeEventsOrganizingSite(Sortie $eventsOrganizingSite): self
    {
        if ($this->eventsOrganizingSite->removeElement($eventsOrganizingSite)) {
            // set the owning side to null (unless already changed)
            if ($eventsOrganizingSite->getOrganizingSite() === $this) {
                $eventsOrganizingSite->setOrganizingSite(null);
            }
        }

        return $this;
    }
}
