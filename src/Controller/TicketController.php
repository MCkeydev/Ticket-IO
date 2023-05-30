<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Operateur;
use App\Entity\Technicien;
use App\Entity\Ticket;
use App\Entity\User;
use App\Entity\Status;
use App\Form\TicketType;
use App\Trait\SuiviTrait;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur pour la gestion des tickets.
 */
class TicketController extends AbstractController
{
    use SuiviTrait;

    /**
     * Crée un nouveau ticket.
     *
     * Cette méthode gère la route "/ticket/create" en utilisant les méthodes "GET" et "POST".
     * Elle crée un nouveau ticket en utilisant les données du formulaire et le persiste en base de données.
     * Seuls les utilisateurs ayant le rôle "ROLE_OPERATEUR" peuvent accéder à cette fonctionnalité.
     *
     * @param EntityManagerInterface $manager L'EntityManager pour accéder à la base de données.
     * @param Request $request La requête HTTP entrante.
     * @return Response La réponse HTTP.
     */
    #[Route(
        "/ticket/create",
        name: "app_ticket_create",
        methods: ["GET", "POST"]
    )]
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

    /**
     * Supprime un ticket.
     *
     * Cette méthode gère la route "/ticket/delete" en utilisant la méthode "DELETE".
     * Elle supprime le ticket spécifié et renvoie une réponse vide.
     * Seuls les utilisateurs ayant le rôle "ROLE_OPERATEUR" peuvent accéder à cette fonctionnalité.
     *
     * @param EntityManagerInterface $entityManager L'EntityManager pour accéder à la base de données.
     * @return Response La réponse HTTP.
     * @throws EntityNotFoundException Si le ticket n'existe pas.
     */
    #[Route(
        "/ticket/delete",
        name: "app_ticket_delete",
        methods: ["DELETE"]
    )]
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
     * Met à jour un ticket.
     *
     * Cette méthode gère la route "/ticket/update/{id}" en utilisant les méthodes "GET" et "POST".
     * Elle met à jour les informations du ticket spécifié en utilisant les données de la requête HTTP.
     * Seuls les utilisateurs ayant le rôle "ROLE_OPERATEUR" ou "ROLE_TECHNICIEN" peuvent accéder à cette fonctionnalité.
     *
     * @param Ticket $ticket Le ticket à mettre à jour.
     * @param EntityManagerInterface $manager L'EntityManager pour accéder à la base de données.
     * @param Request $request La requête HTTP entrante.
     * @return Response La réponse HTTP.
     * @throws EntityNotFoundException Si le ticket n'existe pas.
     * @throws AccessDeniedException Si l'utilisateur n'a pas les permissions nécessaires.
     */
    #[Route(
        "/ticket/update/{id}",
        name: "app_ticket_update",
        methods: ["GET", "POST"]
    )]
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
            'justify' => true,
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
                    "ticket/updateTicket/index.html.twig",
                    [
                        "form" => $form,
                        "ticket" => $ticket,
                    ]
                );
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
                return $this->renderForm(
                    "ticket/updateTicket/index.html.twig",
                    [
                        "form" => $form,
                        "ticket" => $ticket,
                    ]
                );
            }

            /**
             * Si le formulaire possède le champ 'justify' permettant de justifier d'un reroutage,
             * et que la valeur de ce champ est 'null', alors on renvoie le formulaire.
             * (ici cela signifie que le champs à été ajouté au formulaire,
             * mais n'a pas été affiché à l'écran de l'utilisateur)
             */
            if (
                $form->has("justify") &&
                $form->get("justify")->getData() === null
            ) {
                return $this->renderForm(
                    "ticket/updateTicket/index.html.twig",
                    [
                        "form" => $form,
                        "ticket" => $ticket,
                    ]
                );
            }

            /**
             * Si le formulaire possède le champ 'justify', et que ça valeur n'est pas nulle,
             * alors cela veut dire que le reroutage du ticket à été justifié,
             * nous pouvons alors créer le commentaire de reroutage.
             */
            if (
                $form->has("justify") &&
                $form->get("justify")->getData() !== null
            ) {
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

                $comment
                    ->setCommentaire($form->get("justify")->getData())
                    ->setTicket($ticket);
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
                return $this->redirectToRoute("app_accueil");
            }

            return $this->redirectToRoute("app_ticket_suivi", [
                "id" => $ticket->getId(),
            ]);
        }

        return $this->renderForm("ticket/updateTicket/index.html.twig", [
            "form" => $form,
            "ticket" => $ticket,
        ]);
    }

    /**
     * Assigner un technicien à un ticket.
     *
     * Cette méthode gère la route "/ticket/assign/{id}" en utilisant les méthodes "GET" et "POST".
     * Elle permet d'assigner un technicien au ticket spécifié.
     * Seuls les utilisateurs ayant le rôle "ROLE_TECHNICIEN" peuvent accéder à cette fonctionnalité.
     *
     * @param Ticket $ticket Le ticket à assigner.
     * @param EntityManagerInterface $manager L'EntityManager pour accéder à la base de données.
     * @param Request $request La requête HTTP entrante.
     * @return Response La réponse HTTP.
     * @throws AccessDeniedException Si l'utilisateur n'a pas les permissions nécessaires.
     * @throws EntityNotFoundException Si le ticket n'existe pas.
     */
    #[Route(
        "/ticket/assign/{id}",
        name: "app_ticket_assign",
        methods: ["GET", "POST"]
    )]
    public function assignTicket(
        Ticket $ticket,
        EntityManagerInterface $manager,
        Request $request
    ) {
        $currentUser = $this->getUser();

        if (!$currentUser instanceof Technicien) {
            throw $this->createNotFoundException();
        }

        /**
         * Seul un technicien du service concerné est autorisé à assigner un ticket.
         */
        if ($currentUser->getService() !== $ticket->getService()) {
            throw $this->createNotFoundException();
        }

        $techniciensService = $ticket->getService()->getMembres();

        $objects = $this->getTicketSuivi($ticket);

        $form = $this->createFormBuilder($ticket)
            ->add("technicien", EntityType::class, [
                "class" => Technicien::class,
                "placeholder" => "",
                "choices" => $techniciensService,
            ])
            ->add("valider", SubmitType::class, [
                "attr" => ["class" => "button"],
            ])
            ->getForm();

        $form->handleRequest($request);

        // Logique post submit du formulaire s'il est valide.
        if ($form->isSubmitted() && $form->isValid()) {
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
                return $this->redirectToRoute('app_ticket_suivi', ['id' => $ticket->getId()]);

            }
            $uow->commit();

            $ticket->setUpdatedAt(new \DateTimeImmutable());
            $manager->flush();

            return $this->redirectToRoute('app_ticket_suivi', ['id' => $ticket->getId()]);
        }

        return $this->renderForm("suivi/suiviModif/suiviModif.twig.html", [
            "form" => $form,
            "objects" => $objects,
            "ticket" => $ticket,
            "titre" => "Assigner un technicien au ticket",
        ]);
    }

    /**
     * Affiche les tickets assignés au technicien connecté.
     *
     * Cette méthode gère la route "/tickets/mes_tickets" en utilisant la méthode "GET".
     * Elle affiche tous les tickets assignés au technicien connecté.
     * Seuls les utilisateurs ayant le rôle "ROLE_TECHNICIEN" peuvent accéder à cette fonctionnalité.
     *
     * @param EntityManagerInterface $manager L'EntityManager pour accéder à la base de données.
     * @return Response La réponse HTTP.
     */
    #[Route(
        "/tickets/mes_tickets",
        name: "app_tickets_mes_tickets",
        methods: ["GET"]
    )]
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

    /**
     * Affiche les tickets en attente de traitement.
     *
     * Cette méthode gère la route "/tickets/en_attente" en utilisant la méthode "GET".
     * Elle affiche tous les tickets en attente de traitement.
     * Les techniciens n'ont accès qu'aux tickets de leur service,
     * tandis que les opérateurs ont accès à tous les tickets en attente.
     * Seuls les utilisateurs ayant le rôle "ROLE_TECHNICIEN" ou "ROLE_OPERATEUR" peuvent accéder à cette fonctionnalité.
     *
     * @param EntityManagerInterface $manager L'EntityManager pour accéder à la base de données.
     * @return Response La réponse HTTP.
     */
    #[Route(
        "/tickets/en_attente",
        name: "app_tickets_en_attente",
        methods: ["GET"]
    )]
    public function vueTicketsEnAttente(
        EntityManagerInterface $manager
    ): Response {
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

    /**
     * Affiche les tickets clos.
     *
     * Cette méthode gère la route "/tickets/clos" en utilisant la méthode "GET".
     * Elle affiche tous les tickets clos.
     * Les techniciens n'ont accès qu'aux tickets de leur service,
     * tandis que les opérateurs ont accès à tous les tickets clos.
     * Seuls les utilisateurs ayant le rôle "ROLE_TECHNICIEN" ou "ROLE_OPERATEUR" peuvent accéder à cette fonctionnalité.
     *
     * @param EntityManagerInterface $manager L'EntityManager pour accéder à la base de données.
     * @return Response La réponse HTTP.
     */
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
