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

class TacheController extends AbstractController
{
    use SuiviTrait;

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
        // on récupère l'utilisateur connecté
        $currentUser = $this->getUser();
        /**
         * Il n'est possible d'ajouter une tâche que sur un ticket qui n'est pas clos,
         * nous allons alors vérifier le status de ce dernier.
         * Si le ticket n'appartient pas au service du technicien, il n'a pas non plus d'accès.
         */
        if ($currentUser->getService() !== $ticket->getService() || $ticket->getStatus()->getLibelle() === 'Clos') {
            throw $this->createNotFoundException();
        }
        // Nous récupérons tout le suivi du ticket en question
        $objects = $this->getTicketSuivi($ticket);

        // On vient créer le formulaire de la tache, et la future tache.
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
            // on récupère le formulaire
            $tache = $form->getData();
            // Nous mettons à jour la date de dernière MAJ du ticket.
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
