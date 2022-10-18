<?php

namespace App\Entity;

use App\Entity\AbstractEntities\AbstractUserClass;
use App\Repository\TicketRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

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
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\ManyToOne(inversedBy: 'tickets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Service $service = null;

    #[ORM\ManyToOne(inversedBy: 'tickets')]
    private ?User $client = null;

    #[ORM\ManyToOne(inversedBy: 'tickets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Operateur $operateur = null;

    #[ORM\ManyToMany(targetEntity: Technicien::class, inversedBy: 'tickets')]
    private Collection $techniciens;

    #[ORM\ManyToOne(inversedBy: 'tickets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Status $status = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Criticite $criticite = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Gravite $gravite = null;

    #[ORM\OneToMany(mappedBy: 'ticket', targetEntity: Tache::class)]
    private Collection $taches;

    #[ORM\OneToMany(mappedBy: 'ticket', targetEntity: Commentaire::class, orphanRemoval: true)]
    private Collection $commentaires;

    public function __construct()
    {
        $this->techniciens = new ArrayCollection();
        $this->taches = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
        $this->created_at = new DateTimeImmutable();
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
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
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

    /**
     * @return Collection<int, Technicien>
     */
    public function getTechniciens(): Collection
    {
        return $this->techniciens;
    }

    public function addTechnicien(Technicien $technicien): self
    {
        if (!$this->techniciens->contains($technicien)) {
            $this->techniciens->add($technicien);
        }

        return $this;
    }

    public function removeTechnicien(Technicien $technicien): self
    {
        $this->techniciens->removeElement($technicien);

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

}
