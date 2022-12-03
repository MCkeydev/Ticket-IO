<?php

namespace App\Entity;

use App\Repository\CommentaireRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentaireRepository::class)]
class Commentaire
{
	#[ORM\Id]
         	#[ORM\GeneratedValue]
         	#[ORM\Column]
         	private ?int $id = null;

	#[ORM\Column(length: 255)]
         	private ?string $commentaire = null;

	#[ORM\Column]
         	private ?\DateTimeImmutable $created_at = null;

	#[ORM\ManyToOne(inversedBy: "commentaires")]
         	#[ORM\JoinColumn(nullable: false)]
         	private ?Ticket $ticket = null;

    #[ORM\ManyToOne(inversedBy: 'commentaires')]
    #[ORM\JoinColumn(nullable: false)]
    private ?technicien $auteur = null;

	public function getId(): ?int
         	{
         		return $this->id;
         	}

	public function getCommentaire(): ?string
         	{
         		return $this->commentaire;
         	}

	public function setCommentaire(string $commentaire): self
         	{
         		$this->commentaire = $commentaire;
         
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

	public function getTicket(): ?Ticket
         	{
         		return $this->ticket;
         	}

	public function setTicket(?Ticket $ticket): self
         	{
         		$this->ticket = $ticket;
         
         		return $this;
         	}

    public function getAuteur(): ?technicien
    {
        return $this->auteur;
    }

    public function setAuteur(?technicien $auteur): self
    {
        $this->auteur = $auteur;

        return $this;
    }
}
