<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Operateur;
use App\Entity\Technicien;
use App\Entity\Ticket;
use App\Entity\User;
use App\Entity\Status;
use App\Form\TicketType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TicketController extends AbstractController
{
	#[
		Route("/ticket/create", name: "app_ticket_create", methods: ["GET", "POST"])
	]
	public function createTicket(
		EntityManagerInterface $manager,
		Request $request
	): Response {
		$this->denyAccessUnlessGranted("ROLE_OPERATEUR");

		// On récupère l'utilisateur connecté.
		$currentUser = $this->getUser();

		// On vient créer le formulaire du ticket, et le futur ticket.
		$ticket = new Ticket();
		$form = $this->createForm(TicketType::class, $ticket);
		$form->remove("status")->remove("technicien");
		$form->handleRequest($request);

		// Logique post submit du formulaire s'il est valide.
		if ($form->isSubmitted() && $form->isValid()) {
			// On récupère la valeur du champ 'client' dans le formulaire.
			$userEmail = $form->get("client")->getData();
			// On tente de récupérer l'utilisateur dans la BDD.
			$user = $manager
				->getRepository(User::class)
				->findOneBy(["email" => $userEmail]);

			// Si $utilisateur n'est pas défini, c'est que l'utilisateur renseigné n'existe pas.
			if (!$user) {
				// On vient ajouter l'erreur au formulaire.
				$form
					->get("client")
					->addError(
						new FormError("L'utilisateur renseigné n'est pas valide.")
					);

				return $this->renderForm("ticket/createTicket/index.html.twig", [
					"form" => $form,
				]);
			}

			// On vient alors peupler les propriétés du ticket.
			$ticket->setClient($user);

			if ($currentUser instanceof Operateur) {
				$ticket->setOperateur($currentUser);
			}

			$ticket = $form->getData();
			$statusRepository = $manager->getRepository(Status::class);
			$ticket->setStatus($statusRepository->find(1));

			$manager->persist($ticket);
			$manager->flush();

			return $this->redirectToRoute("app_ticket_suivi", [
				"id" => $ticket->getId(),
			]);
		}

		return $this->renderForm("ticket/createTicket/index.html.twig", [
			"form" => $form,
		]);
	}

	#[Route("/ticket/delete", name: "app_ticket_delete", methods: ["DELETE"])]
	public function deleteTicket(EntityManagerInterface $entityManager): Response
	{
		try {
			$ticketRepository = $entityManager->getRepository(Ticket::class);
			$ticket = $ticketRepository->find(4);
			if (!$ticket) {
				throw new EntityNotFoundException("Le ticket n'existe pas");
			}
			$ticketRepository->remove($ticket, true);

			return new Response();
		} catch (\Exception $exception) {
			return new Response($exception->getMessage());
		}
	}

	/**
	 * Habituellement, il serait préférable d'exposer une route d'update à la méthode put.
	 * Cependant les requêtes php n'arrivent pas à récupérer des form data dans les requetes put.
	 * Nous allons donc utiliser la méthode POST
	 */
	#[
		Route(
			"/ticket/update/{id}",
			name: "app_ticket_update",
			methods: ["GET", "POST"]
		)
	]
	public function updateTicket(
		Ticket $ticket,
		EntityManagerInterface $manager,
		Request $request
	): Response {
		// On récupère l'utilisateur connecté
		$currentUser = $this->getUser();
        // Variable permettant de déterminer si l'opération de modification est un reroutage ou pas.
        $isReroutage = false;

		/**
		 * Seuls les opérateurs, et les techniciens du service peuvent modifier un ticket.
		 */
		if (
			!$this->isGranted("ROLE_TECHNICIEN") &&
			!$this->isGranted("ROLE_OPERATEUR")
		) {
			throw $this->createAccessDeniedException();
		}

		if ($currentUser instanceof Technicien) {
			/**
			 * Si l'utilisateur est un technicien et n'appartient pas au service attribué au ticket,
			 * alors on lui refuse l'accès.
			 */
			if ($currentUser->getService() !== $ticket->getService()) {
				throw $this->createAccessDeniedException();
			}
		}

		/**
		 * Nous venons fournir au formulaire l'email du client actuel.
		 */
		$form = $this->createForm(TicketType::class, $ticket, [
			"client_email" => $ticket->getClient()->getEmail(),
		]);
		$form->handleRequest($request);

        $ticketService = $ticket->getService();

		// Logique post submit du formulaire s'il est valide.
		if ($form->isSubmitted() && $form->isValid()) {

			// On récupère la valeur du champ 'client' dans le formulaire.
			$userEmail = $form->get("client")->getData();
			// On tente de recuperate l'utilisateur dans la BDD.
			$user = $manager
				->getRepository(User::class)
				->findOneBy(["email" => $userEmail]);

			// Si $utilisateur n'est pas défini, c'est que l'utilisateur renseigné n'existe pas.
			if (!$user) {
				// On vient ajouter l'erreur au formulaire.
				$form
					->get("client")
					->addError(
						new FormError("L'utilisateur renseigné n'est pas valide.")
					);

				return $this->renderForm("ticket/updateTicket/index.html.twig", [
					"form" => $form,
					"ticket" => $ticket,
				]);
			}
			// On vient alors peupler les propriétés du ticket.
			$ticket->setClient($user);

			if ($currentUser instanceof Operateur) {
				$ticket->setOperateur($currentUser);
			}

			/**
			 * Ici nous ne voulons persister en base de données uniquement si le ticket a changé
			 * (au moins une de ses propriétés a été modifiée)
			 */
			$uow = $manager->getUnitOfWork();
			$ticket = $form->getData();
			$uow->computeChangeSets();
			$ticketChangeSet = $uow->getEntityChangeSet($ticket);

            // Si aucun changement n'a été fait
            if (count($ticketChangeSet) === 0) {
                return $this->renderForm("ticket/updateTicket/index.html.twig", [
                    "form" => $form,
                    "ticket" => $ticket,
                ]);
            }

            /**
             * Si le formulaire possède le champ 'justify' permettant de justifier d'un reroutage,
             * et que la valeur de ce champ est 'null', alors on renvoie le formulaire.
             * (ici cela signifie que le champs à été ajouté au formulaire,
             * mais n'a pas été affiché à l'écran de l'utilisateur)
             */
            if($form->has('justify') && $form->get('justify')->getData() === null) {
                return $this->renderForm("ticket/updateTicket/index.html.twig", [
                    "form" => $form,
                    "ticket" => $ticket,
                ]);
            }

            /**
             * Si le formulaire possède le champ 'justify', et que ça valeur n'est pas nulle,
             * alors cela veut dire que le reroutage du ticket à été justifié,
             * nous pouvons alors créer le commentaire de reroutage.
             */
            if ($form->has('justify') && $form->get('justify')->getData() !== null) {
                /**
                 * Chaque modification du ticket doit s'accompagner d'un commentaire,
                 * c'est ici ce que nous allons faire.
                 */
                $comment = new Commentaire();

                if ($currentUser instanceof Technicien) {
                    $comment->setTechnicien($currentUser);
                } else {
                    $comment->setOperateur($currentUser);
                }

                $comment->setCommentaire($form->get('justify')->getData())->setTicket($ticket);
                $manager->persist($comment);
                $isReroutage = true;
            }

            // Confirmation des modifications faites au ticket
            $uow->commit($ticket);

			// Modification de la date/heure de dernière mise à jour du ticket
			$ticket->setUpdatedAt(new \DateTimeImmutable());

			// Les changements sont persistés dans la base de données
			$manager->flush();

            if ($isReroutage) {
                return $this->redirectToRoute('app_accueil');
            }

            return $this->redirectToRoute("app_ticket_suivi", ['id' => $ticket->getId()]);
        }

		return $this->renderForm("ticket/updateTicket/index.html.twig", [
			"form" => $form,
			"ticket" => $ticket,
		]);
	}
	#[
		Route(
			"/tickets/mes_tickets",
			name: "app_tickets_mes_tickets",
			methods: ["GET"]
		)
	]
	public function vueTicketsMesTickets(
		EntityManagerInterface $manager
	): Response {
		$repository = $manager->getRepository(Ticket::class);
		$currentUser = $this->getUser();
		if ($currentUser instanceof Technicien) {
			$technicien = $currentUser;
			$ticketsMesTickets = $repository->findTechnicienTickets(
				$technicien->getId()
			);
		}
		return $this->render("accueil/accueil.html.twig", [
			"tickets" => $ticketsMesTickets["results"],
			"titre" => "Tous mes tickets",
		]);
	}
	#[
		Route(
			"/tickets/en_attente",
			name: "app_tickets_en_attente",
			methods: ["GET"]
		)
	]
	public function vueTicketsEnAttente(EntityManagerInterface $manager): Response
	{
		$repository = $manager->getRepository(Ticket::class);
		$currentUser = $this->getUser();
		if ($currentUser instanceof Technicien) {
			$service = $currentUser->getService();
			$ticketsEnAttente = $repository->findServiceTickets(
				$service->getId(),
				4,
				false
			);
		}
		if ($currentUser instanceof Operateur) {
			$ticketsEnAttente = $repository->findAllTickets(4, false);
		}
		return $this->render("accueil/accueil.html.twig", [
			"tickets" => $ticketsEnAttente["results"],
			"titre" => "Tickets en attente",
		]);
	}

	#[Route("/tickets/clos", name: "app_tickets_clos", methods: ["GET"])]
	public function vueTicketsClos(EntityManagerInterface $manager): Response
	{
		$repository = $manager->getRepository(Ticket::class);
		$currentUser = $this->getUser();
		if ($currentUser instanceof Technicien) {
			$service = $currentUser->getService();
			$ticketsClos = $repository->findServiceTickets(
				$service->getId(),
				exclude: false
			);
		}
		if ($currentUser instanceof Operateur) {
			$ticketsClos = $repository->findAllTickets(3, false);
		}
		return $this->render("accueil/accueil.html.twig", [
			"tickets" => $ticketsClos["results"],
			"titre" => "Tickets clos",
		]);
	}
}
