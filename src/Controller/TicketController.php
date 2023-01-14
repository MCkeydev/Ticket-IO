<?php

namespace App\Controller;

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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function PHPUnit\Framework\equalTo;

class TicketController extends AbstractController
{
    #[
        Route(
            "/ticket/create",
            name: "app_ticket_create",
            methods: ["GET", "POST"]
        )
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
		$form->remove("status");
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

            return $this->redirectToRoute("app_ticket_suivi", ['id' => $ticket->getId()]);
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

				return $this->renderForm("ticket/createTicket/index.html.twig", [
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
                return new JsonResponse("Le ticket n'a pas changé", Response::HTTP_NOT_MODIFIED);
            }

            // Confirmation des modifications faites au ticket
            $uow->commit($ticket);

            // Modification de la date/heure de dernière mise à jour du ticket
            $ticket->setUpdatedAt(new \DateTimeImmutable());

            // Les changements sont persistés dans la base de données
            $manager->flush();

            return $this->redirectToRoute("app_ticket_suivi", ['id' => $ticket->getId()]);
        }

		return $this->renderForm("ticket/updateTicket/index.html.twig", [
			"form" => $form,
			"ticket" => $ticket,
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
		]);
	}
}
