<?php

namespace App\Entity;

use App\Entity\AbstractEntities\AbstractUserClass;
use App\Repository\ServiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Classe représentant un service dans le système.
 */
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

    /**
     * Renvoie la représentation textuelle du service.
     *
     * @return string La représentation textuelle du service.
     */
    public function __toString(): string
    {
        return $this->nom;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Récupère le nom du service.
     *
     * @return string|null Le nom du service.
     */
    public function getNom(): ?string
    {
        return $this->nom;
    }

    /**
     * Définit le nom du service.
     *
     * @param string $nom Le nom du service.
     * @return self
     */
    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Récupère la collection des tickets associés au service.
     *
     * @return Collection<int, Ticket> La collection des tickets.
     */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    /**
     * Ajoute un ticket à la collection des tickets du service.
     *
     * @param Ticket $ticket Le ticket à ajouter.
     * @return self
     */
    public function addTicket(Ticket $ticket): self
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets->add($ticket);
            $ticket->setService($this);
        }

        return $this;
    }

    /**
     * Supprime un ticket de la collection des tickets du service.
     *
     * @param Ticket $ticket Le ticket à supprimer.
     * @return self
     */
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
     * Récupère la collection des membres (techniciens) associés au service.
     *
     * @return Collection<int, Technicien> La collection des membres.
     */
    public function getMembres(): Collection
    {
        return $this->membres;
    }

    /**
     * Ajoute un membre (technicien) à la collection des membres du service.
     *
     * @param Technicien $membre Le membre à ajouter.
     * @return self
     */
    public function addMembre(Technicien $membre): self
    {
        if (!$this->membres->contains($membre)) {
            $this->membres->add($membre);
            $membre->setService($this);
        }

        return $this;
    }

    /**
     * Supprime un membre (technicien) de la collection des membres du service.
     *
     * @param Technicien $membre Le membre à supprimer.
     * @return self
     */
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
