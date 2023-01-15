<?php

namespace App\Controller;

use App\Entity\Solution;
use App\Entity\Status;
use App\Entity\Technicien;
use App\Entity\Ticket;
use App\Form\SolutionType;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class SolutionController extends AbstractController
{
    #[
        Route(
            "/solution/create/{id}",
            name: "app_solution_create",
            methods: ["get", "post"]
        )
    ]
    public function createSolution(
        Ticket $ticket,
        EntityManagerInterface $manager,
        Request $request
    ): Response {
        $currentUser = $this->getUser();

        /**
         * Il n'est possible d'ajouter une solution que sur un ticket qui n'est pas clos,
         * nous allons alors vérifier le status de ce dernier.
         * Si le ticket n'appartient pas au service du technicien, il n'a pas non plus d'accès.
         */
        if ($currentUser->getService() !== $ticket->getService() || $ticket->getStatus()->getLibelle() === 'Clos') {
            throw $this->createNotFoundException();
        }

        // On récupère l'utilisateur connecté.
        // On vient créer le formulaire du commentaire, et le futur commentaire.
        $solution = new Solution();
        $form = $this->createForm(SolutionType::class, $solution);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($currentUser instanceof Technicien) {
                $solution
                    ->setAuteur($currentUser)
                    ->setTicket($ticket);
            }

            // on récupère le formulaire
            $solution = $form->getData();

            /**
             * Si une solution est ajoutée, alors le ticket doit se clore.
             * Pour se faire nous allons récupérer le status clos dans la bdd.
             * Nous changeons ensuite le status du ticket
             */
            $statusClos = $manager->getRepository(Status::class)->find(3);
            $ticket->setStatus($statusClos);
            // Nous mettons à jour la date de dernière MAJ du ticket.
            $ticket->setUpdatedAt(new DateTimeImmutable());

            $manager->persist($solution);
            $manager->flush();

            return $this->redirectToRoute('app_ticket_suivi', ['id' => $ticket->getId() ]);
        }
        return $this->renderForm("solution/index.html.twig", [
            "form" => $form,
        ]);
    }
}
