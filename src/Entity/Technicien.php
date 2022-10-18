<?php

namespace App\Entity;

use App\Entity\AbstractEntities\AbstractUserClass;
use App\Repository\TechnicienRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TechnicienRepository::class)]
class Technicien extends AbstractUserClass
{

    #[ORM\ManyToOne(inversedBy: 'membres')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Service $service = null;

    #[ORM\ManyToMany(targetEntity: Ticket::class, mappedBy: 'techniciens')]
    private Collection $tickets;

    #[ORM\OneToMany(mappedBy: 'technicien', targetEntity: Tache::class)]
    private Collection $taches;

    #[ORM\OneToMany(mappedBy: 'auteur', targetEntity: Solution::class)]
    private Collection $solutions;

    public function __construct()
    {
        $this->tickets = new ArrayCollection();
        $this->taches = new ArrayCollection();
        $this->solutions = new ArrayCollection();
    }

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): self
    {
        $this->service = $service;

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
            $ticket->addTechnicien($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): self
    {
        if ($this->tickets->removeElement($ticket)) {
            $ticket->removeTechnicien($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Tache>
     */
    public function getTaches(): Collection
    {
        return $this->taches;
    }

    public function addTach(Tache $tach): self
    {
        if (!$this->taches->contains($tach)) {
            $this->taches->add($tach);
            $tach->setTechnicien($this);
        }

        return $this;
    }

    public function removeTach(Tache $tach): self
    {
        if ($this->taches->removeElement($tach)) {
            // set the owning side to null (unless already changed)
            if ($tach->getTechnicien() === $this) {
                $tach->setTechnicien(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Solution>
     */
    public function getSolutions(): Collection
    {
        return $this->solutions;
    }

    public function addSolution(Solution $solution): self
    {
        if (!$this->solutions->contains($solution)) {
            $this->solutions->add($solution);
            $solution->setAuteur($this);
        }

        return $this;
    }

    public function removeSolution(Solution $solution): self
    {
        if ($this->solutions->removeElement($solution)) {
            // set the owning side to null (unless already changed)
            if ($solution->getAuteur() === $this) {
                $solution->setAuteur(null);
            }
        }

        return $this;
    }
}
