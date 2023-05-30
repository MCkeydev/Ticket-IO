<?php

namespace App\Entity;

use App\Repository\CriticiteRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Classe représentant la criticité d'un ticket.
 */
#[ORM\Entity(repositoryClass: CriticiteRepository::class)]
class Criticite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    /**
     * Méthode magique utilisée pour convertir l'objet Criticite en chaîne de caractères.
     *
     * @return string La représentation en chaîne de caractères de la criticité.
     */
    public function __toString()
    {
        return $this->libelle;
    }

    /**
     * Récupère l'ID de la criticité.
     *
     * @return int|null L'ID de la criticité.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Récupère le libellé de la criticité.
     *
     * @return string|null Le libellé de la criticité.
     */
    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    /**
     * Définit le libellé de la criticité.
     *
     * @param string $libelle Le libellé de la criticité.
     * @return self
     */
    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }
}
