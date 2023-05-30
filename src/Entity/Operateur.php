<?php

namespace App\Entity;

use App\Entity\AbstractEntities\AbstractUserClass;
use App\Repository\OperateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Classe représentant un opérateur dans le système.
 */
#[ORM\Entity(repositoryClass: OperateurRepository::class)]
#[UniqueEntity("email", message: "Le compte existe déjà")]
class Operateur extends AbstractUserClass
{
    #[ORM\OneToMany(mappedBy: "operateur", targetEntity: Ticket::class)]
    private Collection $tickets;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\OneToMany(mappedBy: "operateur", targetEntity: Commentaire::class)]
    private Collection $commentaires;

    public function __construct()
    {
        $this->tickets = new ArrayCollection();
        $this->roles = ["ROLE_OPERATEUR"];
        $this->commentaires = new ArrayCollection();
    }

    /**
     * Récupère la collection des tickets associés à l'opérateur.
     *
     * @return Collection<int, Ticket> La collection des tickets.
     */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    /**
     * Ajoute un ticket à la collection des tickets de l'opérateur.
     *
     * @param Ticket $ticket Le ticket à ajouter.
     * @return self
     */
    public function addTicket(Ticket $ticket): self
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets->add($ticket);
            $ticket->setOperateur($this);
        }

        return $this;
    }

    /**
     * Supprime un ticket de la collection des tickets de l'opérateur.
     *
     * @param Ticket $ticket Le ticket à supprimer.
     * @return self
     */
    public function removeTicket(Ticket $ticket): self
    {
        if ($this->tickets->removeElement($ticket)) {
            // set the owning side to null (unless already changed)
            if ($ticket->getOperateur() === $this) {
                $ticket->setOperateur(null);
            }
        }

        return $this;
    }

    /**
     * Récupère le nom de l'opérateur.
     *
     * @return string|null Le nom de l'opérateur.
     */
    public function getNom(): ?string
    {
        return $this->nom;
    }

    /**
     * Définit le nom de l'opérateur.
     *
     * @param string $nom Le nom de l'opérateur.
     * @return self
     */
    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Récupère le prénom de l'opérateur.
     *
     * @return string|null Le prénom de l'opérateur.
     */
    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    /**
     * Définit le prénom de l'opérateur.
     *
     * @param string $prenom Le prénom de l'opérateur.
     * @return self
     */
    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Récupère la collection des commentaires associés à l'opérateur.
     *
     * @return Collection<int, Commentaire> La collection des commentaires.
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    /**
     * Ajoute un commentaire à la collection des commentaires de l'opérateur.
     *
     * @param Commentaire $commentaire Le commentaire à ajouter.
     * @return self
     */
    public function addCommentaire(Commentaire $commentaire): self
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires->add($commentaire);
            $commentaire->setOperateur($this);
        }

        return $this;
    }

    /**
     * Supprime un commentaire de la collection des commentaires de l'opérateur.
     *
     * @param Commentaire $commentaire Le commentaire à supprimer.
     * @return self
     */
    public function removeCommentaire(Commentaire $commentaire): self
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getOperateur() === $this) {
                $commentaire->setOperateur(null);
            }
        }

        return $this;
    }
}
