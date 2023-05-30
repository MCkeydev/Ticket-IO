<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Trait\SuiviTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur pour le suivi des tickets.
 */
class SuiviController extends AbstractController
{
    use SuiviTrait;

    /**
     * Affiche le suivi d'un ticket.
     *
     * Cette méthode gère la route "/ticket/suivi/{id}" en utilisant la méthode "GET".
     * Elle affiche le suivi du ticket spécifié en vérifiant les autorisations de l'utilisateur connecté.
     * Les utilisateurs ayant le rôle "ROLE_OPERATEUR" ou appartenant au service du ticket peuvent interagir avec le ticket.
     *
     * @param Ticket $ticket Le ticket dont afficher le suivi.
     * @return Response La réponse HTTP contenant le suivi du ticket.
     * @throws AccessDeniedException Si l'utilisateur n'est pas autorisé à accéder au suivi du ticket.
     */
    #[Route("/ticket/suivi/{id}", name: "app_ticket_suivi", methods: ["GET"])]
    public function index(Ticket $ticket): Response
    {
        $currentUser = $this->getUser();

        $isAllowed =
            $ticket->getClient() === $currentUser ||
            $this->isGranted("ROLE_OPERATEUR") ||
            $currentUser->getService() === $ticket->getService();

        if (!$isAllowed) {
            throw $this->createAccessDeniedException();
        }

        $objects = $this->getTicketSuivi($ticket);

        return $this->render("suivi/suivi.twig.html", [
            "ticket" => $ticket,
            "objects" => $objects,
            "isAllowed" => $isAllowed,
        ]);
    }
}
