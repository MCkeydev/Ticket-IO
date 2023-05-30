<?php

namespace App\Controller;

use App\Entity\Solution;
use App\Entity\Status;
use App\Entity\Technicien;
use App\Entity\Ticket;
use App\Form\SolutionType;
use App\Trait\SuiviTrait;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur pour la gestion des solutions.
 */
class SolutionController extends AbstractController
{
    use SuiviTrait;

    /**
     * Crée une solution pour un ticket donné.
     *
     * Cette méthode gère la route "/solution/create/{id}" en utilisant les méthodes "GET" et "POST".
     * Elle crée une solution pour le ticket spécifié en vérifiant que le ticket n'est pas clos et appartient au service du technicien connecté.
     * Elle utilise la classe SolutionType pour créer le formulaire de solution.
     *
     * @param Ticket $ticket Le ticket pour lequel créer une solution.
     * @param EntityManagerInterface $manager L'EntityManager pour accéder à la base de données.
     * @param Request $request La requête HTTP entrante.
     * @return Response La réponse HTTP.
     * @throws NotFoundHttpException Si le ticket n'est pas trouvé.
     */
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

        if ($currentUser->getService() !== $ticket->getService() || $ticket->getStatus()->getLibelle() === 'Clos') {
            throw $this->createNotFoundException();
        }

        $objects = $this->getTicketSuivi($ticket);

        $solution = new Solution();
        $form = $this->createForm(SolutionType::class, $solution);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($currentUser instanceof Technicien) {
                $solution
                    ->setAuteur($currentUser)
                    ->setTicket($ticket);
            }

            $solution = $form->getData();

            $statusClos = $manager->getRepository(Status::class)->find(3);
            $ticket->setStatus($statusClos);
            $ticket->setUpdatedAt(new DateTimeImmutable());

            $manager->persist($solution);
            $manager->flush();

            return $this->redirectToRoute('app_ticket_suivi', ['id' => $ticket->getId() ]);
        }
        return $this->renderForm("suivi/suiviModif/suiviModif.twig.html", [
            'titre' => 'Ajouter une solution',
            'ticket' => $ticket,
            'objects' => $objects,
            "form" => $form,
        ]);
    }
}
