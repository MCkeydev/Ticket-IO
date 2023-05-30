<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Contrôleur pour la gestion de la sécurité (connexion et déconnexion).
 */
class SecurityController extends AbstractController
{
    /**
     * Affiche le formulaire de connexion.
     *
     * Cette méthode gère la route "/login" et renvoie le formulaire de connexion.
     * Elle utilise la classe AuthenticationUtils pour récupérer les éventuelles erreurs de connexion.
     *
     * @param AuthenticationUtils $authenticationUtils L'instance d'AuthenticationUtils pour récupérer les informations de connexion.
     * @return Response La réponse HTTP contenant le formulaire de connexion.
     */
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * Gère la déconnexion de l'utilisateur.
     *
     * Cette méthode gère la route "/logout" et ne renvoie aucune réponse.
     * Elle lève une exception de logique car elle sera interceptée par la clé de déconnexion de votre pare-feu.
     * Cette méthode peut être laissée vide.
     *
     * @throws \LogicException Cette exception est levée pour indiquer que cette méthode peut être vide.
     */
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
