<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Technicien;
use App\Entity\Ticket;
use App\Form\CommentaireType;
use App\FormTrait;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentaireController extends AbstractController
{
	use FormTrait;

	#[
		Route(
			"/commentaire/create/{id}",
			name: "app_commentaire_create",
			methods: ["get", "post"]
		)
	]
	public function createCommentaire(
		Ticket $ticket,
		EntityManagerInterface $manager,
		Request $request
	): Response {
		$this->denyAccessUnlessGranted("ROLE_TECHNICIEN");
		// On récupère l'utilisateur connecté.
		$currentUser = $this->getUser();

		// On vient créer le formulaire du commentaire, et le futur commentaire.
		$commentaire = new Commentaire();
		$form = $this->createForm(CommentaireType::class, $commentaire);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			if ($currentUser instanceof Technicien) {
				$commentaire
					->setAuteur($currentUser)
					->setTicket($ticket)
					->setCreatedAt(new DateTimeImmutable());
			}

			// on récupère le formulaire
			$commentaire = $form->getData();
			$manager->persist($commentaire);
			$manager->flush();
		}

		return $this->renderForm("ticket/index.html.twig", [
			"form" => $form,
		]);
	}
}
