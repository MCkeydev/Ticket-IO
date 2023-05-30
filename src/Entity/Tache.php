<?php

namespace App\Entity;

use App\Repository\TacheRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TacheRepository::class)]
class Tache
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?string $temps = null;

    #[ORM\ManyToOne(inversedBy: "taches")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Technicien $auteur = null;

    #[ORM\ManyToOne(inversedBy: "taches")]
    private ?Ticket $ticket = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Description = null;

    /**
     * Obtient l'identifiant de la tâche.
     *
     * @return int|null L'identifiant de la tâche.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Récupère le temps de la tâche.
     *
     * @return string|null Le temps de la tâche.
     */
    public function getTemps(): ?string
    {
        return $this->temps;
    }

    /**
     * Définit le temps de la tâche.
     *
     * @param string $temps Le temps de la tâche.
     * @return self
     */
    public function setTemps(string $temps): self
    {
        $this->temps = $temps;

        return $this;
    }

    /**
     * Obtient l'auteur de la tâche.
     *
     * @return Technicien|null L'auteur de la tâche.
     */
    public function getAuteur(): ?Technicien
    {
        return $this->auteur;
    }

    /**
     * Définit l'auteur de la tâche.
     *
     * @param Technicien $auteur L'auteur de la tâche.
     * @return self
     */
    public function setAuteur(?Technicien $auteur): self
    {
        $this->auteur = $auteur;

        return $this;
    }

    /**
     * Obtient le ticket associé à la tâche.
     *
     * @return Ticket|null Le ticket associé à la tâche.
     */
    public function getTicket(): ?Ticket
    {
        return $this->ticket;
    }

    /**
     * Définit le ticket associé à la tâche.
     *
     * @param Ticket $ticket Le ticket associé à la tâche.
     * @return self
     */
    public function setTicket(?Ticket $ticket): self
    {
        $this->ticket = $ticket;

        return $this;
    }

    /**
     * Obtient la date de création de la tâche.
     *
     * @return \DateTimeImmutable|null La date de création de la tâche.
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    /**
     * Définit la date de création de la tâche.
     *
     * @param \DateTimeImmutable $created_at La date de création de la tâche.
     * @return self
     */
    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * Obtient la description de la tâche.
     *
     * @return string|null La description de la tâche.
     */
    public function getDescription(): ?string
    {
        return $this->Description;
    }

    /**
     * Définit la description de la tâche.
     *
     * @param string|null $Description La description de la tâche.
     * @return self
     */
    public function setDescription(?string $Description): self
    {
        $this->Description = $Description;

        return $this;
    }
}
