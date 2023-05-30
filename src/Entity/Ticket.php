<?php

namespace App\Entity;

use App\Entity\AbstractEntities\AbstractUserClass;
use App\Repository\TicketRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Unique;

/**
 * Classe représentant un ticket dans le système.
 */
#[ORM\Entity(repositoryClass: TicketRepository::class)]
class Ticket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: "tickets")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Service $service = null;

    #[ORM\ManyToOne(inversedBy: "tickets")]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $client = null;

    #[ORM\ManyToOne(inversedBy: "tickets")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Operateur $operateur = null;

    #[ORM\ManyToOne(inversedBy: "tickets")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Status $status = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Criticite $criticite = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Gravite $gravite = null;

    #[ORM\OneToMany(mappedBy: "ticket", targetEntity: Tache::class)]
    private Collection $taches;

    #[ORM\OneToMany(mappedBy: "ticket", targetEntity: Commentaire::class, orphanRemoval: true)]
    private Collection $commentaires;

    #[ORM\OneToMany(mappedBy: "ticket", targetEntity: Solution::class, orphanRemoval: true)]
    private Collection $solutions;

    #[ORM\ManyToOne(inversedBy: "tickets")]
    private ?Technicien $technicien = null;

    public function __construct()
    {
        $this->taches = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->solutions = new ArrayCollection();
    }

    /**
     * Récupère l'identifiant du ticket.
     *
     * @return int|null L'identifiant du ticket.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Récupère le titre du ticket.
     *
     * @return string|null Le titre du ticket.
     */
    public function getTitre(): ?string
    {
        return $this->titre;
    }

    /**
     * Définit le titre du ticket.
     *
     * @param string $titre Le titre du ticket.
     * @return self
     */
    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Récupère la description du ticket.
     *
     * @return string|null La description du ticket.
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Définit la description du ticket.
     *
     * @param string $description La description du ticket.
     * @return self
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Récupère la date de création du ticket.
     *
     * @return \DateTimeImmutable|null La date de création du ticket.
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Récupère le service associé au ticket.
     *
     * @return Service|null Le service associé au ticket.
     */
    public function getService(): ?Service
    {
        return $this->service;
    }

    /**
     * Définit le service associé au ticket.
     *
     * @param Service|null $service Le service associé au ticket.
     * @return self
     */
    public function setService(?Service $service): self
    {
        $this->service = $service;

        return $this;
    }

    /**
     * Récupère le client associé au ticket.
     *
     * @return User|null Le client associé au ticket.
     */
    public function getClient(): ?User
    {
        return $this->client;
    }

    /**
     * Définit le client associé au ticket.
     *
     * @param User|null $client Le client associé au ticket.
     * @return self
     */
    public function setClient(?User $client): self
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Récupère l'opérateur associé au ticket.
     *
     * @return Operateur|null L'opérateur associé au ticket.
     */
    public function getOperateur(): ?Operateur
    {
        return $this->operateur;
    }

    /**
     * Définit l'opérateur associé au ticket.
     *
     * @param Operateur|null $operateur L'opérateur associé au ticket.
     * @return self
     */
    public function setOperateur(?Operateur $operateur): self
    {
        $this->operateur = $operateur;

        return $this;
    }

    /**
     * Récupère le statut du ticket.
     *
     * @return Status|null Le statut du ticket.
     */
    public function getStatus(): ?Status
    {
        return $this->status;
    }

    /**
     * Définit le statut du ticket.
     *
     * @param Status|null $status Le statut du ticket.
     * @return self
     */
    public function setStatus(?Status $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Récupère la criticité du ticket.
     *
     * @return Criticite|null La criticité du ticket.
     */
    public function getCriticite(): ?Criticite
    {
        return $this->criticite;
    }

    /**
     * Définit la criticité du ticket.
     *
     * @param Criticite|null $criticite La criticité du ticket.
     * @return self
     */
    public function setCriticite(?Criticite $criticite): self
    {
        $this->criticite = $criticite;

        return $this;
    }

    /**
     * Récupère la gravité du ticket.
     *
     * @return Gravite|null La gravité du ticket.
     */
    public function getGravite(): ?Gravite
    {
        return $this->gravite;
    }

    /**
     * Définit la gravité du ticket.
     *
     * @param Gravite|null $gravite La gravité du ticket.
     * @return self
     */
    public function setGravite(?Gravite $gravite): self
    {
        $this->gravite = $gravite;

        return $this;
    }

    /**
     * Récupère les tâches associées au ticket.
     *
     * @return Collection<int, Tache> Les tâches associées au ticket.
     */
    public function getTaches(): Collection
    {
        return $this->taches;
    }

    /**
     * Ajoute une tâche à la liste des tâches associées au ticket.
     *
     * @param Tache $tache La tâche à ajouter.
     * @return self
     */
    public function addTache(Tache $tache): self
    {
        if (!$this->taches->contains($tache)) {
            $this->taches->add($tache);
            $tache->setTicket($this);
        }

        return $this;
    }

    /**
     * Supprime une tâche de la liste des tâches associées au ticket.
     *
     * @param Tache $tache La tâche à supprimer.
     * @return self
     */
    public function removeTache(Tache $tache): self
    {
        if ($this->taches->removeElement($tache)) {
            // set the owning side to null (unless already changed)
            if ($tache->getTicket() === $this) {
                $tache->setTicket(null);
            }
        }

        return $this;
    }

    /**
     * Récupère les commentaires associés au ticket.
     *
     * @return Collection<int, Commentaire> Les commentaires associés au ticket.
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    /**
     * Ajoute un commentaire à la liste des commentaires associés au ticket.
     *
     * @param Commentaire $commentaire Le commentaire à ajouter.
     * @return self
     */
    public function addCommentaire(Commentaire $commentaire): self
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires->add($commentaire);
            $commentaire->setTicket($this);
        }

        return $this;
    }

    /**
     * Supprime un commentaire de la liste des commentaires associés au ticket.
     *
     * @param Commentaire $commentaire Le commentaire à supprimer.
     * @return self
     */
    public function removeCommentaire(Commentaire $commentaire): self
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getTicket() === $this) {
                $commentaire->setTicket(null);
            }
        }

        return $this;
    }

    /**
     * Récupère les solutions associées au ticket.
     *
     * @return Collection<int, Solution> Les solutions associées au ticket.
     */
    public function getSolutions(): Collection
    {
        return $this->solutions;
    }

    /**
     * Ajoute une solution à la liste des solutions associées au ticket.
     *
     * @param Solution $solution La solution à ajouter.
     * @return self
     */
    public function addSolution(Solution $solution): self
    {
        if (!$this->solutions->contains($solution)) {
            $this->solutions->add($solution);
            $solution->setTicket($this);
        }

        return $this;
    }

    /**
     * Supprime une solution de la liste des solutions associées au ticket.
     *
     * @param Solution $solution La solution à supprimer.
     * @return self
     */
    public function removeSolution(Solution $solution): self
    {
        if ($this->solutions->removeElement($solution)) {
            // set the owning side to null (unless already changed)
            if ($solution->getTicket() === $this) {
                $solution->setTicket(null);
            }
        }

        return $this;
    }

    /**
     * Récupère le technicien associé au ticket.
     *
     * @return Technicien|null Le technicien associé au ticket.
     */
    public function getTechnicien(): ?Technicien
    {
        return $this->technicien;
    }

    /**
     * Définit le technicien associé au ticket.
     *
     * @param Technicien|null $technicien Le technicien associé au ticket.
     * @return self
     */
    public function setTechnicien(?Technicien $technicien): self
    {
        $this->technicien = $technicien;

        return $this;
    }

    /**
     * Récupère la date de dernière mise à jour du ticket.
     *
     * @return \DateTimeImmutable|null La date de dernière mise à jour du ticket.
     */
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * Définit la date de dernière mise à jour du ticket.
     *
     * @param \DateTimeImmutable|null $updatedAt La date de dernière mise à jour du ticket.
     * @return self
     */
    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
