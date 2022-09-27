<?php

namespace App\Entity;

use App\Entity\AbstractEntities\AbstractUserClass;
use App\Repository\TicketRepository;
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

    public function __construct()
    {
        $this->techniciens = new ArrayCollection();
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

}
