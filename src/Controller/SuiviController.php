<?php

namespace App\Controller;

use App\Entity\Ticket;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SuiviController extends AbstractController
{
    #[Route("/ticket/suivi/{id}", name: "app_ticket_suivi", methods: ["GET"])]
    public function index(Ticket $ticket): Response
    {
        // Nous récupérons les commentaires correspondant au ticket
        $commentaires = $ticket->getCommentaires();
        // Nous récupérons les taches correspondant au ticket
        $taches = $ticket->getTaches();
        // Nous récupérons les potentielles solution au ticket
        $solutions = $ticket->getSolutions();
        $currentUser = $this->getUser();

        // Nous voulons vérifier si l'utilisateur courant est en capacité d'intéragir avec le ticket.
        $isAllowed =
            $ticket->getClient() === $currentUser ||
            $this->isGranted("ROLE_OPERATEUR") ||
            $currentUser->getService() === $ticket->getService();

        /**
         * Afin d'afficher les taches et commentaires dans l'ordre chronologique,
         * nous les ajoutons dans un tableau, et trions celui-ci par ordre croissant
         * (à l'aide de la propriété created_at).
         */
        $objects = array_merge(
            $commentaires->toArray(),
            $taches->toArray(),
            $solutions->toArray()
        );
        usort($objects, function ($a, $b) {
            return $a->getCreatedAt() < $b->getCreatedAt() ? 1 : -1;
        });

        return $this->render("suivi/suivi.twig.html", [
            "ticket" => $ticket,
            "objects" => $objects,
            "isAllowed" => $isAllowed,
        ]);
    }
}
