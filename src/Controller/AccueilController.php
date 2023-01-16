<?php

namespace App\Controller;

use App\Entity\Technicien;
use App\Entity\Operateur;
use App\Entity\Ticket;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccueilController extends AbstractController
{
	#[Route("/accueil", name: "app_accueil", methods: ["get"])]
	public function index(EntityManagerInterface $manager): Response
	{
		$currentUser = $this->getUser();

		if ($currentUser instanceof Technicien) {
			return $this->accueilTechnicien($manager);
		} elseif ($currentUser instanceof Operateur) {
			return $this->accueilOperateur($manager);
		} elseif ($currentUser instanceof User) {
			return $this->accueilUser();
		}
		return new RedirectResponse($this->generateUrl("app_login"));
	}

	private function accueilTechnicien(EntityManagerInterface $manager): Response
	{
		$repository = $manager->getRepository(Ticket::class);
		$currentUser = $this->getUser();
		$service = $currentUser->getService();
		$tickets = $repository->findServiceTickets(
			$service->getId(),
			exclude: true
		);

		return $this->render("accueil/accueil.html.twig", [
			"tickets" => $tickets["results"],
			"titre" => "Tous les tickets du service",
		]);
	}

	private function accueilOperateur(EntityManagerInterface $manager): Response
	{
		$tickets = $manager->getRepository(Ticket::class)->findAllTickets();

		return $this->render("accueil/accueil.html.twig", [
			"tickets" => $tickets["results"],
			"isOperateur" => true,
			"titre" => "Tous les tickets",
		]);
	}

	private function accueilUser(): Response
	{
		$currentUser = $this->getUser();
		$tickets = $currentUser->getTickets();

		return $this->render("accueil/accueil.html.twig", [
			"tickets" => $tickets,
			"titre" => "Tous mes tickets",
		]);
	}
}
