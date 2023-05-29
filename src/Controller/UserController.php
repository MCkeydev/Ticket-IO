<?php

namespace App\Controller;

use App\Entity\Operateur;
use App\Entity\Technicien;
use App\Entity\User;
use App\Form\UserType;
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

class UserController extends AbstractController
{
	use FormTrait;

	#[Route("/user/create", name: "app_user_create")]
	public function createUser(
		EntityManagerInterface $manager,
		Request $request,
		UserPasswordHasherInterface $hasher
	): Response {
		$user = new User();
		$form = $this->createForm(UserType::class, $user);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$user = $form->getData();

			if (
				$form->get("password")->getData() !==
				$form->get("confirmation")->getData()
			) {
				$form
					->get("password")
					->addError(new FormError("les 2 mdp ne sont pas identiques"));
			}
			if ($this->checkExistingUser($manager, $form->get("email"))) {
				$form
					->get("email")
					->addError(new FormError("Cet email est déjà prise."));
			}
			if ($this->checkErrors($form->all())) {
				return $this->renderForm("user/index.html.twig", [
					"form" => $form,
				]);
			}

			$user->setPassword(
				$hasher->hashPassword($user, $form->get("password")->getData())
			);
			$manager->persist($user);
			$manager->flush();

			return $this->redirectToRoute("app_ticket_create");
		}

		return $this->renderForm("user/index.html.twig", [
			"form" => $form,
		]);
	}

	#[Route("/user/delete", name: "app_user_delete", methods: ["DELETE"])]
	public function deleteUser(EntityManagerInterface $manager): Response
	{
		try {
			$userRepository = $manager->getRepository(User::class);
			$user = $userRepository->find(2);
			if (!$user) {
				throw new EntityNotFoundException("Le compte n'existe pas");
			}
			$userRepository->remove($user, true);
			return new Response();
		} catch (\Exception $exception) {
			return new Response($exception->getMessage());
		}
	}

	#[Route("/user/update/{id}", name: "app_user_update")]
	public function updateUser(
		EntityManagerInterface $manager,
		Request $request,
		UserPasswordHasherInterface $hasher,
		int $id
	): Response {
		// TODO : convertir en JSON
		$user = $manager->getRepository(User::class)->find($id);
		if (!$user) {
			throw new EntityNotFoundException("L'utilisateur n'existe pas");
		}
		$user
			->setEmail($request->get("email"))
			->setPassword($hasher->hashPassword($user, $request->get("mdp")));
		$manager->flush();
		return new Response($request->getContent());
	}
}
