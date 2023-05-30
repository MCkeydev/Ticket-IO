<?php

namespace App\Entity;

use App\Repository\GraviteRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Classe représentant la gravité d'un ticket.
 */
#[ORM\Entity(repositoryClass: GraviteRepository::class)]
class Gravite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    /**
     * Méthode magique utilisée pour convertir l'objet Gravite en chaîne de caractères.
     *
     * @return string La représentation en chaîne de caractères de la gravité.
     */
    public function __toString()
    {
        return $this->libelle;
    }

    /**
     * Récupère l'ID de la gravité.
     *
     * @return int|null L'ID de la gravité.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Récupère le libellé de la gravité.
     *
     * @return string|null Le libellé de la gravité.
     */
    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    /**
     * Définit le libellé de la gravité.
     *
     * @param string $libelle Le libellé de la gravité.
     * @return self
     */
    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }
}
