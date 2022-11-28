<?php

namespace App\Controller;

use App\Entity\Ticket;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SuiviController extends AbstractController
{
    #[Route('/ticket/suivi/{id}', name: 'app_ticket_suivi', methods: ['GET'])]
    public function index(Ticket $ticket): Response {
        $commentaires = $ticket->getCommentaires();

        return $this->render('suivi/suivi.twig.html', [
            'ticket' => $ticket,
            'commentaires' => $commentaires,
        ]);
    }
}