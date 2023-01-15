<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Operateur;
use App\Entity\Technicien;
use App\Entity\Ticket;
use App\Form\CommentaireType;
use App\FormTrait;
use App\Trait\SuiviTrait;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentaireController extends AbstractController
{
	use FormTrait;
    use SuiviTrait;
	#[
		Route(
			"/commentaire/create/{id}",
			name: "app_commentaire_create",
			methods: ["GET", "POST"]
		)
	]
	public function createCommentaire(
		Ticket $ticket,
		EntityManagerInterface $manager,
		Request $request
	): Response {
        // On récupère l'utilisateur connecté.
        $currentUser = $this->getUser();

        // Nous récupérons tout le suivi du ticket en question
        $objects = $this->getTicketSuivi($ticket);

        /**
         * Il n'est possible d'ajouter un commentaire que sur un ticket qui n'est pas clos,
         * nous allons alors vérifier le status de ce dernier.
         * Si le ticket n'appartient pas au service du technicien, il n'a pas non plus d'accès.
         */
        if ($currentUser->getService() !== $ticket->getService() || $ticket->getStatus()->getLibelle() === 'Clos') {
            throw $this->createNotFoundException();
        }

		// On vient créer le formulaire du commentaire, et le futur commentaire.
		$commentaire = new Commentaire();
		$form = $this->createForm(CommentaireType::class, $commentaire);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			if ($currentUser instanceof Technicien) {
				$commentaire->setTechnicien($currentUser);
			} else if ($currentUser instanceof Operateur) {
                $commentaire->setOperateur($currentUser);
            }

			// on récupère le formulaire
			$commentaire = $form->getData();

            // Nous mettons à jour la date de dernière MAJ du ticket.
            $ticket->setUpdatedAt(new DateTimeImmutable());
            $commentaire->setTicket($ticket);

			$manager->persist($commentaire);
			$manager->flush();

            return $this->redirectToRoute('app_ticket_suivi', ['id' => $ticket->getId() ]);
		}

        return $this->renderForm("suivi/suiviModif/suiviModif.twig.html", [
            'titre' => 'Ajouter un commentaire',
            'ticket' => $ticket,
            'objects' => $objects,
            "form" => $form,
        ]);
	}
}
