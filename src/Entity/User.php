<?php

namespace App\Entity;

use App\Entity\AbstractEntities\AbstractUserClass;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Classe représentant un utilisateur client.
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity("email", message: "Le compte existe déjà")]
class User extends AbstractUserClass
{
    #[ORM\OneToMany(mappedBy: "client", targetEntity: Ticket::class)]
    private Collection $tickets;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    public function __construct()
    {
        $this->tickets = new ArrayCollection();
        $this->setRoles(["ROLE_USER"]);
    }

    /**
     * Récupère les tickets associés à l'utilisateur.
     *
     * @return Collection<int, Ticket> Les tickets associés à l'utilisateur.
     */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    /**
     * Ajoute un ticket à la liste des tickets associés à l'utilisateur.
     *
     * @param Ticket $ticket Le ticket à ajouter.
     * @return self
     */
    public function addTicket(Ticket $ticket): self
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets->add($ticket);
            $ticket->setClient($this);
        }

        return $this;
    }

    /**
     * Supprime un ticket de la liste des tickets associés à l'utilisateur.
     *
     * @param Ticket $ticket Le ticket à supprimer.
     * @return self
     */
    public function removeTicket(Ticket $ticket): self
    {
        if ($this->tickets->removeElement($ticket)) {
            // set the owning side to null (unless already changed)
            if ($ticket->getClient() === $this) {
                $ticket->setClient(null);
            }
        }

        return $this;
    }

    /**
     * Récupère le nom de l'utilisateur.
     *
     * @return string|null Le nom de l'utilisateur.
     */
    public function getNom(): ?string
    {
        return $this->nom;
    }

    /**
     * Définit le nom de l'utilisateur.
     *
     * @param string $nom Le nom de l'utilisateur.
     * @return self
     */
    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Récupère le prénom de l'utilisateur.
     *
     * @return string|null Le prénom de l'utilisateur.
     */
    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    /**
     * Définit le prénom de l'utilisateur.
     *
     * @param string $prenom Le prénom de l'utilisateur.
     * @return self
     */
    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }
}
