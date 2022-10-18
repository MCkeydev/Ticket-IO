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

    #[ORM\Column(length: 255)]
    private ?string $temps = null;

    #[ORM\ManyToOne(inversedBy: 'taches')]
    #[ORM\JoinColumn(nullable: false)]
    private ?technicien $technicien = null;

    #[ORM\ManyToOne(inversedBy: 'taches')]
    private ?Ticket $ticket = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTemps(): ?string
    {
        return $this->temps;
    }

    public function setTemps(string $temps): self
    {
        $this->temps = $temps;

        return $this;
    }

    public function getTechnicien(): ?technicien
    {
        return $this->technicien;
    }

    public function setTechnicien(?technicien $technicien): self
    {
        $this->technicien = $technicien;

        return $this;
    }

    public function getTicket(): ?Ticket
    {
        return $this->ticket;
    }

    public function setTicket(?Ticket $ticket): self
    {
        $this->ticket = $ticket;

        return $this;
    }
}
