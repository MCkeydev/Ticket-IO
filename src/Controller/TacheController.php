<?php

namespace App\Controller;

use App\Entity\Tache;
use App\Entity\Technicien;
use App\Entity\Ticket;
use App\Form\TacheType;
use App\Trait\SuiviTrait;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur pour la gestion des tâches.
 */
class TacheController extends AbstractController
{
    use SuiviTrait;

    /**
     * Crée une tâche pour un ticket donné.
     *
     * Cette méthode gère la route "/tache/create/{id}" en utilisant les méthodes "GET" et "POST".
     * Elle crée une tâche pour le ticket spécifié en vérifiant que le ticket n'est pas clos et appartient au service du technicien connecté.
     * Elle utilise la classe TacheType pour créer le formulaire de tâche.
     *
     * @param Ticket $ticket Le ticket pour lequel créer une tâche.
     * @param EntityManagerInterface $manager L'EntityManager pour accéder à la base de données.
     * @param Request $request La requête HTTP entrante.
     * @return Response La réponse HTTP.
     * @throws NotFoundHttpException Si le ticket n'est pas trouvé.
     */
    #[
        Route(
            "/tache/create/{id}",
            name: "app_tache_create",
            methods: ["get", "post"]
        )
    ]
    public function createTache(
        Ticket $ticket,
        EntityManagerInterface $manager,
        Request $request
    ): Response {
        $currentUser = $this->getUser();

        if ($currentUser->getService() !== $ticket->getService() || $ticket->getStatus()->getLibelle() === 'Clos') {
            throw $this->createNotFoundException();
        }

        $objects = $this->getTicketSuivi($ticket);

        $tache = new Tache();
        $form = $this->createForm(TacheType::class, $tache);
        $form->handlerequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($currentUser instanceof Technicien) {
                $tache
                    ->setAuteur($currentUser)
                    ->setTicket($ticket)
                    ->setCreatedAt(new DateTimeImmutable());
            }

            $tache = $form->getData();
            $ticket->setUpdatedAt(new DateTimeImmutable());

            $manager->persist($tache);
            $manager->flush();

            return $this->redirectToRoute('app_ticket_suivi', ['id' => $ticket->getId() ]);
        }
        return $this->renderForm("suivi/suiviModif/suiviModif.twig.html", [
            'titre' => 'Ajouter une tâche',
            'ticket' => $ticket,
            'objects' => $objects,
            "form" => $form,
        ]);
    }
}
