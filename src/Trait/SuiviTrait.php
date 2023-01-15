<?php

namespace App\Trait;

use App\Entity\Ticket;

trait SuiviTrait
{
    public function getTicketSuivi(Ticket $ticket): array {
        // Nous récupérons les commentaires correspondant au ticket
        $commentaires = $ticket->getCommentaires();
        // Nous récupérons les taches correspondant au ticket
        $taches = $ticket->getTaches();
        // Nous récupérons les potentielles solution au ticket
        $solutions = $ticket->getSolutions();

        /**
         * Afin d'afficher les taches et commentaires dans l'ordre chronologique,
         * nous les ajoutons dans un tableau, et trions celui-ci par ordre croissant
         * (à l'aide de la propriété created_at).
         */
        $elementsSuivi = array_merge(
            $commentaires->toArray(),
            $taches->toArray(),
            $solutions->toArray()
        );

        usort($elementsSuivi, function ($a, $b) {
            return $a->getCreatedAt() < $b->getCreatedAt() ? 1 : -1;
        });

        return $elementsSuivi;
    }
}