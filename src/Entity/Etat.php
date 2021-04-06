<?php

namespace App\Entity;

use App\Repository\EtatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EtatRepository::class)
 */
class Etat
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $wording;

    /**
     * @ORM\OneToMany(targetEntity=Sortie::class, mappedBy="state")
     */
    private $eventsExistingList;

    public function __construct()
    {
        $this->eventsExistingList = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWording(): ?string
    {
        return $this->wording;
    }

    public function setWording(string $wording): self
    {
        $this->wording = $wording;

        return $this;
    }

    /**
     * @return Collection|Sortie[]
     */
    public function getEventsExistingList(): Collection
    {
        return $this->eventsExistingList;
    }

    public function addEventsExistingList(Sortie $eventsExistingList): self
    {
        if (!$this->eventsExistingList->contains($eventsExistingList)) {
            $this->eventsExistingList[] = $eventsExistingList;
            $eventsExistingList->setState($this);
        }

        return $this;
    }

    public function removeEventsExistingList(Sortie $eventsExistingList): self
    {
        if ($this->eventsExistingList->removeElement($eventsExistingList)) {
            // set the owning side to null (unless already changed)
            if ($eventsExistingList->getState() === $this) {
                $eventsExistingList->setState(null);
            }
        }

        return $this;
    }
}
