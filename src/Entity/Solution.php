<?php

namespace App\Entity;

use App\Repository\SolutionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\NotBlank;

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getAuteur(): ?Technicien
    {
        return $this->auteur;
    }

    public function setAuteur(?Technicien $auteur): self
    {
        $this->auteur = $auteur;

        return $this;
    }

    public function getTicket(): ?Ticket
    {
        return $this->ticket;
    }

    public function setTicket(Ticket $ticket): self
    {
        $this->ticket = $ticket;

        return $this;
    }

    public function getSolution(): ?string
    {
        return $this->solution;
    }

    public function setSolution(string $solution): self
    {
        $this->solution = $solution;

        return $this;
    }
}
