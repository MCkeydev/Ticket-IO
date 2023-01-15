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

class DevController extends AbstractController
{
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

	#[Route("/dev/tickets", name: "app_dev_ticketfactory", methods: "GET")]
	public function ticketFactory()
	{
		TicketFactory::createMany(50);

		return new Response("yahoo");
	}

	#[Route("/dev/reroute", name: "app_dev_reroute", methods: "GET")]
	public function reroute()
	{
		return $this->render("ticket/rerouteTicket/rerouteTicket.html.twig");
	}
}
