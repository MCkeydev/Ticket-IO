<?php

namespace App\Entity;

use App\Repository\CommentaireRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\NotBlank;

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
    private ?Technicien $technicien = null;

    #[ORM\ManyToOne(inversedBy: 'commentaires')]
    private ?Operateur $operateur = null;

    public function __construct()
    {
        $this->created_at = new \DateTimeImmutable();
    }

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

	public function getTicket(): ?Ticket
                  	{
                  		return $this->ticket;
                  	}

	public function setTicket(?Ticket $ticket): self
                  	{
                  		$this->ticket = $ticket;
                  
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

    public function getOperateur(): ?Operateur
    {
        return $this->operateur;
    }

    public function setOperateur(?Operateur $operateur): self
    {
        $this->operateur = $operateur;

        return $this;
    }
}
