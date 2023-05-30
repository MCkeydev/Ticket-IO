<?php

namespace App\Entity;

use App\Repository\CommentaireRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Classe représentant un commentaire.
 */
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
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: "commentaires")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ticket $ticket = null;

    #[ORM\ManyToOne(inversedBy: 'commentaires')]
    private ?Technicien $technicien = null;

    #[ORM\ManyToOne(inversedBy: 'commentaires')]
    private ?Operateur $operateur = null;

    /**
     * Constructeur de la classe Commentaire.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    /**
     * Récupère l'ID du commentaire.
     *
     * @return int|null L'ID du commentaire.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Récupère le contenu du commentaire.
     *
     * @return string|null Le contenu du commentaire.
     */
    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    /**
     * Définit le contenu du commentaire.
     *
     * @param string $commentaire Le contenu du commentaire.
     * @return self
     */
    public function setCommentaire(string $commentaire): self
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    /**
     * Récupère la date/heure de création du commentaire.
     *
     * @return \DateTimeImmutable|null La date/heure de création du commentaire.
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Récupère le ticket associé au commentaire.
     *
     * @return Ticket|null Le ticket associé au commentaire.
     */
    public function getTicket(): ?Ticket
    {
        return $this->ticket;
    }

    /**
     * Définit le ticket associé au commentaire.
     *
     * @param Ticket|null $ticket Le ticket associé au commentaire.
     * @return self
     */
    public function setTicket(?Ticket $ticket): self
    {
        $this->ticket = $ticket;

        return $this;
    }

    /**
     * Récupère le technicien associé au commentaire.
     *
     * @return Technicien|null Le technicien associé au commentaire.
     */
    public function getTechnicien(): ?Technicien
    {
        return $this->technicien;
    }

    /**
     * Définit le technicien associé au commentaire.
     *
     * @param Technicien|null $technicien Le technicien associé au commentaire.
     * @return self
     */
    public function setTechnicien(?Technicien $technicien): self
    {
        $this->technicien = $technicien;

        return $this;
    }

    /**
     * Récupère l'opérateur associé au commentaire.
     *
     * @return Operateur|null L'opérateur associé au commentaire.
     */
    public function getOperateur(): ?Operateur
    {
        return $this->operateur;
    }

    /**
     * Définit l'opérateur associé au commentaire.
     *
     * @param Operateur|null $operateur L'opérateur associé au commentaire.
     * @return self
     */
    public function setOperateur(?Operateur $operateur): self
    {
        $this->operateur = $operateur;

        return $this;
    }
}
