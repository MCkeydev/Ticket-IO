<?php

namespace App\Entity;

use App\Entity\AbstractEntities\AbstractUserClass;
use App\Repository\TechnicienRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: TechnicienRepository::class)]
#[UniqueEntity("email", message: "Le compte existe déjà")]
class Technicien extends AbstractUserClass
{
	#[ORM\ManyToOne(inversedBy: "membres")]
	#[ORM\JoinColumn(nullable: false)]
	private ?Service $service = null;

	#[ORM\OneToMany(mappedBy: "auteur", targetEntity: Tache::class)]
	private Collection $taches;

	#[ORM\OneToMany(mappedBy: "auteur", targetEntity: Solution::class)]
	private Collection $solutions;

	#[ORM\Column(length: 255)]
	private ?string $nom = null;

	#[ORM\Column(length: 255)]
	private ?string $prenom = null;

	#[ORM\OneToMany(mappedBy: "technicien", targetEntity: Commentaire::class)]
	private Collection $commentaires;

	#[ORM\OneToMany(mappedBy: "technicien", targetEntity: Ticket::class)]
	private Collection $tickets;

	public function __construct()
	{
		$this->tickets = new ArrayCollection();
		$this->taches = new ArrayCollection();
		$this->solutions = new ArrayCollection();
		$this->roles = ["ROLE_TECHNICIEN"];
		$this->commentaires = new ArrayCollection();
		$this->ticketss = new ArrayCollection();
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

	/**
	 * @return Collection<int, Ticket>
	 */
	public function getTickets(): Collection
	{
		return $this->tickets;
	}

	public function addTicket(Ticket $ticket): self
	{
		if (!$this->tickets->contains($ticket)) {
			$this->tickets->add($ticket);
			$ticket->addTechnicien($this);
		}

		return $this;
	}

	public function removeTicket(Ticket $ticket): self
	{
		if ($this->tickets->removeElement($ticket)) {
			$ticket->removeTechnicien($this);
		}

		return $this;
	}

	/**
	 * @return Collection<int, Tache>
	 */
	public function getTaches(): Collection
	{
		return $this->taches;
	}

	public function addTach(Tache $tache): self
	{
		if (!$this->taches->contains($tache)) {
			$this->taches->add($tache);
			$tache->setAuteur($this);
		}

		return $this;
	}

	public function removeTach(Tache $tache): self
	{
		if ($this->taches->removeElement($tache)) {
			// set the owning side to null (unless already changed)
			if ($tache->getAuteur() === $this) {
				$tache->setAuteur(null);
			}
		}

		return $this;
	}

	/**
	 * @return Collection<int, Solution>
	 */
	public function getSolutions(): Collection
	{
		return $this->solutions;
	}

	public function addSolution(Solution $solution): self
	{
		if (!$this->solutions->contains($solution)) {
			$this->solutions->add($solution);
			$solution->setAuteur($this);
		}

		return $this;
	}

	public function removeSolution(Solution $solution): self
	{
		if ($this->solutions->removeElement($solution)) {
			// set the owning side to null (unless already changed)
			if ($solution->getAuteur() === $this) {
				$solution->setAuteur(null);
			}
		}

		return $this;
	}

	public function getNom(): ?string
	{
		return $this->nom;
	}

	public function setNom(string $nom): self
	{
		$this->nom = $nom;

		return $this;
	}

	public function getPrenom(): ?string
	{
		return $this->prenom;
	}

	public function setPrenom(string $prenom): self
	{
		$this->prenom = $prenom;

		return $this;
	}

	/**
	 * @return Collection<int, Commentaire>
	 */
	public function getCommentaires(): Collection
	{
		return $this->commentaires;
	}

	public function addCommentaire(Commentaire $commentaire): self
	{
		if (!$this->commentaires->contains($commentaire)) {
			$this->commentaires->add($commentaire);
			$commentaire->setTechnicien($this);
		}

		return $this;
	}

	public function removeCommentaire(Commentaire $commentaire): self
	{
		if ($this->commentaires->removeElement($commentaire)) {
			// set the owning side to null (unless already changed)
			if ($commentaire->getTechnicien() === $this) {
				$commentaire->setTechnicien(null);
			}
		}

		return $this;
	}
	public function __toString(): string
	{
		return $this->getEmail();
	}

	/**
	 * @return Collection<int, Ticket>
	 */
}
