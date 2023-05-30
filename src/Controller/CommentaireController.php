<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Operateur;
use App\Entity\Technicien;
use App\Entity\Ticket;
use App\Form\CommentaireType;
use App\Trait\FormTrait;
use App\Trait\SuiviTrait;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur pour la création d'un commentaire sur un ticket.
 */
class CommentaireController extends AbstractController
{
    use FormTrait;
    use SuiviTrait;

    /**
     * Crée un commentaire pour un ticket spécifié.
     *
     * Cette méthode gère la route "/commentaire/create/{id}" en utilisant les méthodes "GET" et "POST".
     * Elle crée un commentaire pour le ticket donné et le relie au ticket.
     * Seuls les techniciens et opérateurs autorisés peuvent ajouter des commentaires.
     *
     * @param Ticket $ticket Le ticket pour lequel ajouter le commentaire.
     * @param EntityManagerInterface $manager L'EntityManager pour accéder à la base de données.
     * @param Request $request La requête HTTP entrante.
     * @return Response La réponse HTTP redirigeant vers le suivi du ticket après l'ajout du commentaire.
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException Si le ticket est clos ou si l'utilisateur n'a pas accès au ticket.
     */
    #[Route(
        "/commentaire/create/{id}",
        name: "app_commentaire_create",
        methods: ["GET", "POST"]
    )]
    public function createCommentaire(
        Ticket $ticket,
        EntityManagerInterface $manager,
        Request $request
    ): Response {
        // On récupère l'utilisateur connecté.
        $currentUser = $this->getUser();

        // Nous récupérons tout le suivi du ticket en question
        $objects = $this->getTicketSuivi($ticket);

        // Vérification du statut du ticket et de l'accès de l'utilisateur
        if ($ticket->getStatus()->getLibelle() === "Clos") {
            throw $this->createNotFoundException();
        }
        if (
            !$currentUser instanceof Operateur &&
            $currentUser instanceof Technicien &&
            $currentUser->getService() !== $ticket->getService()
        ) {
            throw $this->createNotFoundException();
        }

        // On vient créer le formulaire du commentaire, et le futur commentaire.
        $commentaire = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($currentUser instanceof Technicien) {
                $commentaire->setTechnicien($currentUser);
            } elseif ($currentUser instanceof Operateur) {
                $commentaire->setOperateur($currentUser);
            }

            // On récupère les données du formulaire
            $commentaire = $form->getData();

            // Mise à jour de la date de dernière mise à jour du ticket
            $ticket->setUpdatedAt(new DateTimeImmutable());
            $commentaire->setTicket($ticket);

            $manager->persist($commentaire);
            $manager->flush();

            return $this->redirectToRoute("app_ticket_suivi", [
                "id" => $ticket->getId(),
            ]);
        }

        return $this->renderForm("suivi/suiviModif/suiviModif.twig.html", [
            "titre" => "Ajouter un commentaire",
            "ticket" => $ticket,
            "objects" => $objects,
            "form" => $form,
        ]);
    }
}
