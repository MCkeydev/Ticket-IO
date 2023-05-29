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

    #[
        ORM\OneToMany(
            mappedBy: "ticket",
            targetEntity: Commentaire::class,
            orphanRemoval: true
        )
    ]
    private Collection $commentaires;

    #[
        ORM\OneToMany(
            mappedBy: "ticket",
            targetEntity: Solution::class,
            orphanRemoval: true
        )
    ]
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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
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

    public function getClient(): ?User
    {
        return $this->client;
    }

    public function setClient(?User $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getOperateur(): ?Operateur
    {
        return $this->operateur;
    }

    public function setOperateur(?Operateur $operateur): self
    {
        $this->operateur = $operateur;

        return $this;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCriticite(): ?Criticite
    {
        return $this->criticite;
    }

    public function setCriticite(?Criticite $criticite): self
    {
        $this->criticite = $criticite;

        return $this;
    }

    public function getGravite(): ?Gravite
    {
        return $this->gravite;
    }

    public function setGravite(?Gravite $gravite): self
    {
        $this->gravite = $gravite;

        return $this;
    }

    /**
     * @return Collection<int, taches>
     */
    public function getTaches(): Collection
    {
        return $this->taches;
    }

    public function addTache(Tache $taches): self
    {
        if (!$this->taches->contains($taches)) {
            $this->taches->add($taches);
            $taches->setTicket($this);
        }

        return $this;
    }

    public function removeTache(Tache $taches): self
    {
        if ($this->taches->removeElement($taches)) {
            // set the owning side to null (unless already changed)
            if ($taches->getTicket() === $this) {
                $taches->setTicket(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Commentaire>
     */

    /**
     * @return Collection<int, Commentaire>
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): self
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires->add($commentaire);
            $commentaire->setTicket($this);
        }

        return $this;
    }

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
    public function __toString(): string
    {
        return $this->getTitre();
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
            $solution->setTicket($this);
        }

        return $this;
    }

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

    public function getTechnicien(): ?Technicien
    {
        return $this->technicien;
    }

    public function setTechnicien(?Technicien $technicien): self
    {
        $this->technicien = $technicien;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
