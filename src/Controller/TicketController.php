<?php

namespace App\Controller;

use App\Entity\Operateur;
use App\Entity\Technicien;
use App\Entity\Ticket;
use App\Entity\User;
use App\Form\TicketType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\InvalidTypeException;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
                        new FormError(
                            "L'utilisateur renseigné n'est pas valide."
                        )
                    );

                return $this->renderForm(
                    "ticket/createTicket/index.html.twig",
                    [
                        "form" => $form,
                    ]
                );
            }

            // On vient alors peupler les propriétés du ticket.
            $ticket->setClient($user);

            if ($currentUser instanceof Operateur) {
                $ticket->setOperateur($currentUser);
            }

            $ticket = $form->getData();

            $manager->persist($ticket);
            $manager->flush();

            return $this->redirectToRoute("LEZGO");
        }

        return $this->renderForm("ticket/createTicket/index.html.twig", [
            "form" => $form,
        ]);
    }

    #[Route("/ticket/delete", name: "app_ticket_delete", methods: ["DELETE"])]
    public function deleteTicket(
        EntityManagerInterface $entityManager
    ): Response {
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
    #[Route("/ticket/update/{id}", name: "app_ticket_update", methods: ["GET"])]
    public function updateTicket(
        Ticket $ticket,
        EntityManagerInterface $manager,
        Request $request,
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
                        new FormError(
                            "L'utilisateur renseigné n'est pas valide."
                        )
                    );

                return $this->renderForm(
                    "ticket/createTicket/index.html.twig",
                    [
                        "form" => $form,
                        'ticket' => $ticket,
                    ]
                );
            }
            // On vient alors peupler les propriétés du ticket.
            $ticket->setClient($user);

            if ($currentUser instanceof Operateur) {
                $ticket->setOperateur($currentUser);
            }

            $ticket = $form->getData();

            $manager->persist($ticket);
            $manager->flush();

            return $this->redirectToRoute("LEZGO");
        }

        return $this->renderForm("ticket/updateTicket/index.html.twig", [
            "form" => $form,
            'ticket' => $ticket,
        ]);
    }

    #[Route("/tickets/clos", name: "app_tickets_clos", methods: ["GET"])]
    public function vueTicketsClos(EntityManagerInterface $manager): Response
    {
        $repository = $manager->getRepository(Ticket::class);
        $currentUser = $this->getUser();
        $service = $currentUser->getService();
        $ticketsClos = $repository->findServiceTickets(
            $service->getId(),
            exclude: false
        );
        return $this->render("accueil/accueil.html.twig", [
            "tickets" => $ticketsClos,
        ]);
    }
}
