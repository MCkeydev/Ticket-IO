<?php

namespace App\Controller;

use App\Entity\Operateur;
use App\Form\OperateurType;
use App\Trait\FormTrait;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur pour la gestion des opérateurs.
 */
class OperateurController extends AbstractController
{
    use FormTrait;

    /**
     * Crée un nouvel opérateur.
     *
     * Cette méthode gère la route "/operateur/create" en utilisant la méthode "GET".
     * Elle crée un nouvel opérateur en utilisant les données du formulaire et le persiste en base de données.
     * Seuls les utilisateurs ayant le rôle "ROLE_OPERATEUR" peuvent accéder à cette fonctionnalité.
     *
     * @param EntityManagerInterface $manager L'EntityManager pour accéder à la base de données.
     * @param Request $request La requête HTTP entrante.
     * @param UserPasswordHasherInterface $hasher L'interface UserPasswordHasher pour hasher les mots de passe.
     * @return Response La réponse HTTP.
     */
    #[Route("/operateur/create", name: "app_operateur_create")]
    public function createOperateur(
        EntityManagerInterface $manager,
        Request $request,
        UserPasswordHasherInterface $hasher
    ) {
        $this->denyAccessUnlessGranted("ROLE_OPERATEUR");

        $operateur = new Operateur();
        $form = $this->createForm(OperateurType::class, $operateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $operateur = $form->getData();

            if (
                $form->get("password")->getData() !==
                $form->get("confirmation")->getData()
            ) {
                $form
                    ->get("password")
                    ->addError(new FormError("Le mot de passe est différent"));
            }
            if ($this->checkExistingUser($manager, $form->get("email"))) {
                $form
                    ->get("email")
                    ->addError(new FormError("Le mail est déjà utilisé"));
            }
            if ($this->checkErrors($form->all())) {
                return $this->renderForm("operateur/index.html.twig", [
                    "form" => $form,
                ]);
            }
            $operateur->setPassword(
                $hasher->hashPassword($operateur, $form->get("password")->getData())
            );
            $manager->persist($operateur);
            $manager->flush();
            return $this->redirectToRoute("task_success");
        }
        return $this->renderForm("operateur/index.html.twig", [
            "form" => $form,
        ]);
    }

    /**
     * Supprime un opérateur.
     *
     * Cette méthode gère la route "/operateur/delete" en utilisant la méthode "DELETE".
     * Elle supprime l'opérateur spécifié et renvoie une réponse vide.
     * Seuls les utilisateurs ayant le rôle "ROLE_OPERATEUR" peuvent accéder à cette fonctionnalité.
     *
     * @param ManagerRegistry $registre Le registre du Manager pour accéder à la base de données.
     * @return Response La réponse HTTP.
     * @throws EntityNotFoundException Si le compte opérateur n'existe pas.
     */
    #[Route(
        "/operateur/delete",
        name: "app_operateur_delete",
        methods: ["DELETE"]
    )]
    public function deleteOperateur(ManagerRegistry $registre): Response
    {
        try {
            $this->denyAccessUnlessGranted("ROLE_OPERATEUR");

            $operateurRepository = $registre->getRepository(Operateur::class);
            $operateur = $operateurRepository->find(3);
            if (!$operateur) {
                throw new EntityNotFoundException("Le compte n'existe pas");
            }
            $operateurRepository->remove($operateur, true);

            return new Response();
        } catch (\Exception $exception) {
            return new Response($exception->getMessage());
        }
    }

    /**
     * Met à jour un opérateur.
     *
     * Cette méthode gère la route "/operateur/update/{id}" en utilisant les méthodes "PUT" et "POST".
     * Elle met à jour les informations de l'opérateur spécifié en utilisant les données de la requête HTTP.
     * Seuls les utilisateurs ayant le rôle "ROLE_OPERATEUR" peuvent accéder à cette fonctionnalité.
     *
     * @param ManagerRegistry $registre Le registre du Manager pour accéder à la base de données.
     * @param Request $request La requête HTTP entrante.
     * @param UserPasswordHasherInterface $hasher L'interface UserPasswordHasher pour hasher les mots de passe.
     * @param int $id L'ID de l'opérateur à mettre à jour.
     * @return Response La réponse HTTP.
     * @throws EntityNotFoundException Si le compte opérateur n'existe pas.
     */
    #[Route(
        "/operateur/update/{id}",
        name: "app_operateur_update",
        methods: ["PUT", "POST"]
    )]
    public function updateOperateur(
        ManagerRegistry $registre,
        Request $request,
        UserPasswordHasherInterface $hasher,
        int $id
    ): Response {
        try {
            $this->denyAccessUnlessGranted("ROLE_OPERATEUR");

            $manager = $registre->getManager();
            $operateur = $manager->getRepository(Operateur::class)->find($id);
            if (!$operateur) {
                throw new EntityNotFoundException("Le compte n'existe pas");
            }
            $operateur
                ->setEmail($request->get("email"))
                ->setPassword($hasher->hashPassword($operateur, $request->get("mdp")));
            $manager->flush();
            return new Response();
        } catch (\Exception $exception) {
            return new Response($exception->getMessage());
        }
    }
}
