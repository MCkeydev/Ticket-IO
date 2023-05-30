<?php

namespace App\Entity;

use App\Repository\SolutionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Classe représentant une solution à un ticket dans le système.
 */
#[ORM\Entity(repositoryClass: SolutionRepository::class)]
class Solution
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[NotBlank]
    private ?string $solution = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: "solutions")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Technicien $auteur = null;

    #[ORM\ManyToOne(inversedBy: "solutions")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ticket $ticket = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Récupère la date de création de la solution.
     *
     * @return \DateTimeImmutable|null La date de création.
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Récupère l'auteur de la solution.
     *
     * @return Technicien|null L'auteur de la solution.
     */
    public function getAuteur(): ?Technicien
    {
        return $this->auteur;
    }

    /**
     * Définit l'auteur de la solution.
     *
     * @param Technicien|null $auteur L'auteur de la solution.
     * @return self
     */
    public function setAuteur(?Technicien $auteur): self
    {
        $this->auteur = $auteur;

        return $this;
    }

    /**
     * Récupère le ticket associé à la solution.
     *
     * @return Ticket|null Le ticket associé à la solution.
     */
    public function getTicket(): ?Ticket
    {
        return $this->ticket;
    }

    /**
     * Définit le ticket associé à la solution.
     *
     * @param Ticket $ticket Le ticket associé à la solution.
     * @return self
     */
    public function setTicket(Ticket $ticket): self
    {
        $this->ticket = $ticket;

        return $this;
    }

    /**
     * Récupère la solution.
     *
     * @return string|null La solution.
     */
    public function getSolution(): ?string
    {
        return $this->solution;
    }

    /**
     * Définit la solution.
     *
     * @param string $solution La solution.
     * @return self
     */
    public function setSolution(string $solution): self
    {
        $this->solution = $solution;

        return $this;
    }
}
