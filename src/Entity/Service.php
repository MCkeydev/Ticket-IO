<?php

namespace App\Entity;

use App\Entity\AbstractEntities\AbstractUserClass;
use App\Repository\ServiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ServiceRepository::class)]
class Service
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;



    #[ORM\OneToMany(mappedBy: 'service', targetEntity: Ticket::class, orphanRemoval: true)]
    private Collection $tickets;

    #[ORM\OneToMany(mappedBy: 'service', targetEntity: Technicien::class, orphanRemoval: true)]
    private Collection $membres;

    public function __construct()
    {
        $this->tickets = new ArrayCollection();
        $this->membres = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }


    /**
     * @return Collection<int, Ticket>
     */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    public function addTicket(Ticket $ticket): self
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets->add($ticket);
            $ticket->setService($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): self
    {
        if ($this->tickets->removeElement($ticket)) {
            // set the owning side to null (unless already changed)
            if ($ticket->getService() === $this) {
                $ticket->setService(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Technicien>
     */
    public function getMembres(): Collection
    {
        return $this->membres;
    }

    public function addMembre(Technicien $membre): self
    {
        if (!$this->membres->contains($membre)) {
            $this->membres->add($membre);
            $membre->setService($this);
        }

        return $this;
    }

    public function removeMembre(Technicien $membre): self
    {
        if ($this->membres->removeElement($membre)) {
            // set the owning side to null (unless already changed)
            if ($membre->getService() === $this) {
                $membre->setService(null);
            }
        }

        return $this;
    }
}
