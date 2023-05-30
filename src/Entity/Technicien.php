<?php

namespace App\Entity;

use App\Entity\AbstractEntities\AbstractUserClass;
use App\Repository\TechnicienRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Classe représentant un technicien dans le système.
 */
#[ORM\Entity(repositoryClass: TechnicienRepository::class)]
#[UniqueEntity("email", message: "Le compte existe déjà")]
class Technicien extends AbstractUserClass
{
    #[ORM\ManyToOne(inversedBy: "membres")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Service $service = null;

    #[ORM\OneToMany(mappedBy: "auteur", targetEntity: Tache::class)]
    private Collection $taches;

    #[ORM\OneToMany(mappedBy: "auteur", targetEntity: Solution::class)]
    private Collection $solutions;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\OneToMany(mappedBy: "technicien", targetEntity: Commentaire::class)]
    private Collection $commentaires;

    #[ORM\OneToMany(mappedBy: "technicien", targetEntity: Ticket::class)]
    private Collection $tickets;

    public function __construct()
    {
        $this->tickets = new ArrayCollection();
        $this->taches = new ArrayCollection();
        $this->solutions = new ArrayCollection();
        $this->roles = ["ROLE_TECHNICIEN"];
        $this->commentaires = new ArrayCollection();
        $this->ticketss = new ArrayCollection();
    }

    /**
     * Récupère le service auquel le technicien est rattaché.
     *
     * @return Service|null Le service auquel le technicien est rattaché.
     */
    public function getService(): ?Service
    {
        return $this->service;
    }

    /**
     * Définit le service auquel le technicien est rattaché.
     *
     * @param Service|null $service Le service auquel le technicien est rattaché.
     * @return self
     */
    public function setService(?Service $service): self
    {
        $this->service = $service;

        return $this;
    }

    /**
     * Récupère les tickets associés à ce technicien.
     *
     * @return Collection<int, Ticket> Les tickets associés à ce technicien.
     */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    /**
     * Ajoute un ticket à la liste des tickets associés à ce technicien.
     *
     * @param Ticket $ticket Le ticket à ajouter.
     * @return self
     */
    public function addTicket(Ticket $ticket): self
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets->add($ticket);
            $ticket->addTechnicien($this);
        }

        return $this;
    }

    /**
     * Supprime un ticket de la liste des tickets associés à ce technicien.
     *
     * @param Ticket $ticket Le ticket à supprimer.
     * @return self
     */
    public function removeTicket(Ticket $ticket): self
    {
        if ($this->tickets->removeElement($ticket)) {
            $ticket->removeTechnicien($this);
        }

        return $this;
    }

    /**
     * Récupère les tâches attribuées à ce technicien.
     *
     * @return Collection<int, Tache> Les tâches attribuées à ce technicien.
     */
    public function getTaches(): Collection
    {
        return $this->taches;
    }

    /**
     * Ajoute une tâche à la liste des tâches attribuées à ce technicien.
     *
     * @param Tache $tache La tâche à ajouter.
     * @return self
     */
    public function addTache(Tache $tache): self
    {
        if (!$this->taches->contains($tache)) {
            $this->taches->add($tache);
            $tache->setAuteur($this);
        }

        return $this;
    }

    /**
     * Supprime une tâche de la liste des tâches attribuées à ce technicien.
     *
     * @param Tache $tache La tâche à supprimer.
     * @return self
     */
    public function removeTache(Tache $tache): self
    {
        if ($this->taches->removeElement($tache)) {
            // set the owning side to null (unless already changed)
            if ($tache->getAuteur() === $this) {
                $tache->setAuteur(null);
            }
        }

        return $this;
    }

    /**
     * Récupère les solutions créées par ce technicien.
     *
     * @return Collection<int, Solution> Les solutions créées par ce technicien.
     */
    public function getSolutions(): Collection
    {
        return $this->solutions;
    }

    /**
     * Ajoute une solution à la liste des solutions créées par ce technicien.
     *
     * @param Solution $solution La solution à ajouter.
     * @return self
     */
    public function addSolution(Solution $solution): self
    {
        if (!$this->solutions->contains($solution)) {
            $this->solutions->add($solution);
            $solution->setAuteur($this);
        }

        return $this;
    }

    /**
     * Supprime une solution de la liste des solutions créées par ce technicien.
     *
     * @param Solution $solution La solution à supprimer.
     * @return self
     */
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

    /**
     * Récupère le nom du technicien.
     *
     * @return string|null Le nom du technicien.
     */
    public function getNom(): ?string
    {
        return $this->nom;
    }

    /**
     * Définit le nom du technicien.
     *
     * @param string $nom Le nom du technicien.
     * @return self
     */
    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Récupère le prénom du technicien.
     *
     * @return string|null Le prénom du technicien.
     */
    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    /**
     * Définit le prénom du technicien.
     *
     * @param string $prenom Le prénom du technicien.
     * @return self
     */
    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Récupère les commentaires créés par ce technicien.
     *
     * @return Collection<int, Commentaire> Les commentaires créés par ce technicien.
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    /**
     * Ajoute un commentaire à la liste des commentaires créés par ce technicien.
     *
     * @param Commentaire $commentaire Le commentaire à ajouter.
     * @return self
     */
    public function addCommentaire(Commentaire $commentaire): self
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires->add($commentaire);
            $commentaire->setTechnicien($this);
        }

        return $this;
    }

    /**
     * Supprime un commentaire de la liste des commentaires créés par ce technicien.
     *
     * @param Commentaire $commentaire Le commentaire à supprimer.
     * @return self
     */
    public function removeCommentaire(Commentaire $commentaire): self
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getTechnicien() === $this) {
                $commentaire->setTechnicien(null);
            }
        }

        return $this;
    }

    /**
     * Retourne l'adresse e-mail du technicien.
     *
     * @return string L'adresse e-mail du technicien.
     */
    public function __toString(): string
    {
        return $this->getEmail();
    }
}
