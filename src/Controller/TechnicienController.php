<?php

namespace App\Controller;

use App\Entity\Service;
use App\Entity\Technicien;
use App\Form\TechnicienType;
use App\Trait\FormTrait;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur pour la gestion des techniciens.
 */
class TechnicienController extends AbstractController
{
    use FormTrait;

    /**
     * Crée un technicien.
     *
     * Cette méthode gère la route "/technicien/create" et crée un nouveau technicien en utilisant les données du formulaire.
     * Elle vérifie les autorisations de l'utilisateur connecté (ROLE_TECHNICIEN) avant de créer le technicien.
     * Elle utilise la classe TechnicienType pour créer le formulaire de technicien.
     *
     * @param EntityManagerInterface $manager L'EntityManager pour accéder à la base de données.
     * @param Request $request La requête HTTP entrante.
     * @param UserPasswordHasherInterface $hasher L'interface pour hasher les mots de passe des utilisateurs.
     * @return Response La réponse HTTP.
     */
    #[Route("/technicien/create", name: "app_technicien_create")]
    public function createTechnicien(
        EntityManagerInterface $manager,
        Request $request,
        UserPasswordHasherInterface $hasher
    ): Response {
        $this->denyAccessUnlessGranted("ROLE_TECHNICIEN");

        $technicien = new Technicien();
        $form = $this->createForm(TechnicienType::class, $technicien);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $technicien = $form->getData();

            if (
                $form->get("password")->getData() !==
                $form->get("confirmation")->getData()
            ) {
                $form
                    ->get("password")
                    ->addError(
                        new FormError("les 2 mdp ne sont pas identiques")
                    );
            }
            if ($this->checkExistingUser($manager, $form->get("email"))) {
                $form
                    ->get("email")
                    ->addError(new FormError("cet Email existe déja"));
            }
            if ($this->checkErrors($form->all())) {
                return $this->renderForm("technicien/index.html.twig", [
                    "form" => $form,
                ]);
            }

            $technicien->setPassword(
                $hasher->hashPassword(
                    $technicien,
                    $form->get("password")->getData()
                )
            );
            $manager->persist($technicien);
            $manager->flush();

            return $this->redirectToRoute("task_success");
        }

        return $this->renderForm("technicien/index.html.twig", [
            "form" => $form,
        ]);
    }

    /**
     * Supprime un technicien.
     *
     * Cette méthode gère la route "/technicien/delete" en utilisant la méthode "DELETE".
     * Elle supprime le technicien avec l'ID spécifié dans la requête.
     * Elle utilise le ManagerRegistry pour accéder au repository Technicien et supprimer l'entité correspondante.
     *
     * @param ManagerRegistry $registre Le ManagerRegistry pour accéder aux entités et aux repositories.
     * @param SerializerInterface $serializer L'interface de sérialisation pour sérialiser les objets.
     * @param Request $request La requête HTTP entrante.
     * @return Response La réponse HTTP.
     * @throws EntityNotFoundException Si le technicien n'est pas trouvé.
     */
    #[
        Route(
            "/technicien/delete",
            name: "app_technicien_delete",
            methods: ["DELETE"]
        )
    ]
    public function deleteTechnicien(
        ManagerRegistry $registre,
        SerializerInterface $serializer,
        Request $request
    ): Response {
        try {
            $technicienRepository = $registre->getRepository(Technicien::class);
            $technicien = $technicienRepository->find(3);
            if (!$technicien) {
                throw new EntityNotFoundException("Le compte n'existe pas");
            }
            $technicienRepository->remove($technicien, true);
            return new Response();
        } catch (\Exception $exception) {
            return new Response($exception->getMessage());
        }
    }

    /**
     * Met à jour un technicien.
     *
     * Cette méthode gère la route "/technicien/update/{id}" en utilisant les méthodes "PUT" et "POST".
     * Elle met à jour le technicien avec l'ID spécifié dans la requête en utilisant les données fournies.
     * Elle utilise le ManagerRegistry pour accéder au repository Technicien et mettre à jour l'entité correspondante.
     * Elle utilise également le UserPasswordHasherInterface pour hasher le mot de passe du technicien.
     *
     * @param ManagerRegistry $registre Le ManagerRegistry pour accéder aux entités et aux repositories.
     * @param Request $request La requête HTTP entrante.
     * @param UserPasswordHasherInterface $hasher L'interface pour hasher les mots de passe des utilisateurs.
     * @param int $id L'ID du technicien à mettre à jour.
     * @return Response La réponse HTTP.
     * @throws EntityNotFoundException Si le technicien n'est pas trouvé.
     */
    #[
        Route(
            "/technicien/update/{id}",
            name: "app_technicien_update",
            methods: ["PUT", "POST"]
        )
    ]
    public function updateTechnicien(
        ManagerRegistry $registre,
        Request $request,
        UserPasswordHasherInterface $hasher,
        int $id
    ): Response {
        // TODO : convertir en JSON
        $manager = $registre->getManager();
        $technicien = $manager->getRepository(Technicien::class)->find($id);
        if (!$technicien) {
            throw new EntityNotFoundException("Le technicien n'existe pas");
        }
        $service = $registre
            ->getRepository(Service::class)
            ->findOneBy(["nom" => $request->get("service")]);
        $technicien
            ->setEmail($request->get("email"))
            ->setService($service)
            ->setPassword(
                $hasher->hashPassword($technicien, $request->get("mdp"))
            );
        $manager->flush();
        return new Response($request->getContent());
    }
}
