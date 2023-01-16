<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Trait\SuiviTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SuiviController extends AbstractController
{
    use SuiviTrait;

    #[Route("/ticket/suivi/{id}", name: "app_ticket_suivi", methods: ["GET"])]
    public function index(Ticket $ticket): Response
    {

        $currentUser = $this->getUser();

        // Nous voulons vérifier si l'utilisateur courant est en capacité d'intéragir avec le ticket.
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
