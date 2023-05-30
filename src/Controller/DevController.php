<?php

namespace App\Controller;

use App\Entity\Service;
use App\Entity\Operateur;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Factory\TicketFactory;

/**
 * Contrôleur pour les fonctionnalités de développement.
 */
class DevController extends AbstractController
{
    /**
     * Crée un utilisateur de test.
     *
     * Cette méthode gère la route "/dev/createUser" en utilisant la méthode "GET".
     * Elle crée un utilisateur de test avec un rôle "ROLE_DEV" et un opérateur de test avec un rôle "ROLE_OPE".
     *
     * @param EntityManagerInterface $entityManager L'EntityManager pour accéder à la base de données.
     * @param UserPasswordHasherInterface $hasher L'interface UserPasswordHasher pour hasher les mots de passe.
     * @return Response La réponse HTTP.
     */
    #[Route("/dev/createUser", name: "app_dev_create_user", methods: "get")]
    public function index(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $hasher
    ): Response {
        $user = new User();
        $user
            ->setEmail("f@f.fr")
            ->setPassword($hasher->hashPassword($user, "password"))
            ->setRoles(["ROLE_DEV"]);
        $entityManager->persist($user);

        $operateur = new Operateur();
        $operateur
            ->setEmail("o@o.fr")
            ->setPassword($hasher->hashPassword($operateur, 'password"'))
            ->setRoles(["ROLE_OPE"]);
        $entityManager->persist($operateur);

        $service = new Service();
        $service->setNom("Test");
        $entityManager->persist($service);

        $entityManager->flush();

        return new Response();
    }

    /**
     * Crée plusieurs tickets de test.
     *
     * Cette méthode gère la route "/dev/tickets" en utilisant la méthode "GET".
     * Elle utilise la classe TicketFactory pour créer 50 tickets de test.
     *
     * @return Response La réponse HTTP.
     */
    #[Route("/dev/tickets", name: "app_dev_ticketfactory", methods: "GET")]
    public function ticketFactory()
    {
        TicketFactory::createMany(50);

        return new Response("yahoo");
    }

    /**
     * Affiche la page de reroutage des tickets de test.
     *
     * Cette méthode gère la route "/dev/reroute" en utilisant la méthode "GET".
     * Elle renvoie la vue de la page de reroutage des tickets de test.
     *
     * @return Response La réponse HTTP contenant la page de reroutage des tickets.
     */
    #[Route("/dev/reroute", name: "app_dev_reroute", methods: "GET")]
    public function reroute()
    {
        return $this->render("ticket/rerouteTicket/rerouteTicket.html.twig");
    }
}
