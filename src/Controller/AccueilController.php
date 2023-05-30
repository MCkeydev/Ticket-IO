<?php

namespace App\Controller;

use App\Entity\Technicien;
use App\Entity\Operateur;
use App\Entity\Ticket;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur pour la page d'accueil en fonction du type d'utilisateur connecté.
 */
class AccueilController extends AbstractController
{
    /**
     * Affiche la page d'accueil en fonction du type d'utilisateur connecté.
     *
     * Cette méthode gère la route "/accueil" et renvoie la page d'accueil correspondante
     * en fonction du type d'utilisateur connecté.
     *
     * @param EntityManagerInterface $manager L'EntityManager pour accéder à la base de données.
     * @return Response La réponse HTTP contenant la page d'accueil.
     */
    #[Route("/accueil", name: "app_accueil", methods: ["get"])]
    public function index(EntityManagerInterface $manager): Response
    {
        $currentUser = $this->getUser();

        if ($currentUser instanceof Technicien) {
            return $this->accueilTechnicien($manager);
        } elseif ($currentUser instanceof Operateur) {
            return $this->accueilOperateur($manager);
        } elseif ($currentUser instanceof User) {
            return $this->accueilUser();
        }
        return new RedirectResponse($this->generateUrl("app_login"));
    }

    /**
     * Affiche la page d'accueil pour un technicien.
     *
     * @param EntityManagerInterface $manager L'EntityManager pour accéder à la base de données.
     * @return Response La réponse HTTP contenant la page d'accueil pour un technicien.
     */
    private function accueilTechnicien(EntityManagerInterface $manager): Response
    {
        $repository = $manager->getRepository(Ticket::class);
        $currentUser = $this->getUser();
        $service = $currentUser->getService();
        $tickets = $repository->findServiceTickets(
            $service->getId(),
            exclude: true
        );

        return $this->render("accueil/accueil.html.twig", [
            "tickets" => $tickets["results"],
            "titre" => "Tous les tickets du service",
        ]);
    }

    /**
     * Affiche la page d'accueil pour un opérateur.
     *
     * @param EntityManagerInterface $manager L'EntityManager pour accéder à la base de données.
     * @return Response La réponse HTTP contenant la page d'accueil pour un opérateur.
     */
    private function accueilOperateur(EntityManagerInterface $manager): Response
    {
        $tickets = $manager->getRepository(Ticket::class)->findAllTickets();

        return $this->render("accueil/accueil.html.twig", [
            "tickets" => $tickets["results"],
            "isOperateur" => true,
            "titre" => "Tous les tickets",
        ]);
    }

    /**
     * Affiche la page d'accueil pour un utilisateur.
     *
     * @return Response La réponse HTTP contenant la page d'accueil pour un utilisateur.
     */
    private function accueilUser(): Response
    {
        $currentUser = $this->getUser();
        $tickets = $currentUser->getTickets();

        return $this->render("accueil/accueil.html.twig", [
            "tickets" => $tickets,
            "titre" => "Tous mes tickets",
        ]);
    }
}
