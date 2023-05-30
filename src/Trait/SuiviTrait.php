<?php

namespace App\Trait;

use App\Entity\Ticket;

/**
 * Trait SuiviTrait
 *
 * Trait contenant une méthode pour récupérer les éléments de suivi d'un ticket.
 */
trait SuiviTrait
{
    /**
     * Méthode getTicketSuivi
     *
     * Récupère les commentaires, les tâches et les solutions associés à un ticket
     * et les retourne dans un tableau trié par ordre chronologique.
     *
     * @param Ticket $ticket Le ticket pour lequel récupérer les éléments de suivi.
     * @return array Un tableau contenant les éléments de suivi du ticket triés par ordre chronologique.
     */
    public function getTicketSuivi(Ticket $ticket): array
    {
        // Nous récupérons les commentaires correspondant au ticket
        $commentaires = $ticket->getCommentaires();
        // Nous récupérons les tâches correspondant au ticket
        $taches = $ticket->getTaches();
        // Nous récupérons les potentielles solutions au ticket
        $solutions = $ticket->getSolutions();

        /**
         * Afin d'afficher les tâches et commentaires dans l'ordre chronologique,
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