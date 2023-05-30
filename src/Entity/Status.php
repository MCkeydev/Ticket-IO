<?php

namespace App\Entity;

use App\Repository\StatusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Classe représentant le statut d'un ticket dans le système.
 */
#[ORM\Entity(repositoryClass: StatusRepository::class)]
class Status
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\OneToMany(mappedBy: 'status', targetEntity: Ticket::class)]
    private Collection $tickets;

    public function __construct()
    {
        $this->tickets = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->libelle;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Récupère le libellé du statut.
     *
     * @return string|null Le libellé du statut.
     */
    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    /**
     * Définit le libellé du statut.
     *
     * @param string $libelle Le libellé du statut.
     * @return self
     */
    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Récupère les tickets associés à ce statut.
     *
     * @return Collection<int, Ticket> Les tickets associés à ce statut.
     */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    /**
     * Ajoute un ticket à la liste des tickets associés à ce statut.
     *
     * @param Ticket $ticket Le ticket à ajouter.
     * @return self
     */
    public function addTicket(Ticket $ticket): self
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets->add($ticket);
            $ticket->setStatus($this);
        }

        return $this;
    }

    /**
     * Supprime un ticket de la liste des tickets associés à ce statut.
     *
     * @param Ticket $ticket Le ticket à supprimer.
     * @return self
     */
    public function removeTicket(Ticket $ticket): self
    {
        if ($this->tickets->removeElement($ticket)) {
            // set the owning side to null (unless already changed)
            if ($ticket->getStatus() === $this) {
                $ticket->setStatus(null);
            }
        }

        return $this;
    }
}
