<?php

namespace App\Controller;
// renseignement des chemins utilisés dans le controller
use App\Entity\Operateur;
use App\Form\OperateurType;
use App\FormTrait;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class OperateurController extends AbstractController // Le controller Operateur utilise les paramètres de AbstractColtroller
{
	use FormTrait; // utilisation des paramètres de formulaire par défaut // URL pour création d'opérateur
	#[Route("/operateur/create", name: "app_operateur_create")]
	public function createOperateur(
		EntityManagerInterface $manager,
		Request $request,
		UserPasswordHasherInterface $hasher
	) {
		// déclaration des variables avec leurs type utile à la fonction
		$this->denyAccessUnlessGranted("ROLE_OPERATEUR");
		$operateur = new Operateur(); // ajout objet Operateur dans la variable $operateur
		$form = $this->createForm(OperateurType::class, $operateur); // formulaire utilisant le modèle OperateurType
		$form->handleRequest($request); // envoie les données du formulaire dans la variable request

		// si le formulaire envoyé est valide
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
					->addError(new FormError("Le mail est déja utilisé"));
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

	#[
		Route(
			"/operateur/delete",
			name: "app_operateur_delete",
			methods: ["DELETE"]
		)
	]
	public function deleteOperateur(ManagerRegistry $registre): Response
	{
		try {
			$this->denyAccessUnlessGranted("ROLE_OPERATEUR"); // pour s'assurer que c'est bien un opérateur.

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
	#[
		Route(
			"/operateur/update/{id}",
			name: "app_operateur_update",
			methods: ["PUT", "POST"]
		)
	]
	public function updateOperateur(
		ManagerRegistry $registre,
		Request $request,
		UserPasswordHasherInterface $hasher,
		int $id
	): Response {
		//TODO : convertir en JSON
		try {
			$this->denyAccessUnlessGranted("ROLE_OPERATEUR"); // pour s'assurer que c'est bien un opérateur.

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
