<?php

namespace App\Controller;

use App\Entity\Solution;
use App\Entity\Technicien;
use App\Entity\Ticket;
use App\Form\SolutionType;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SolutionController extends AbstractController
{
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
        $this->denyAccessUnlessGranted("ROLE_TECHNICIEN");
        // On récupère l'utilisateur connecté.
        $currentUser = $this->getUser();
        // On vient créer le formulaire du commentaire, et le futur commentaire.
        $solution = new Solution();
        $form = $this->createForm(SolutionType::class, $solution);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($currentUser instanceof Technicien) {
                $solution
                    ->setAuteur($currentUser)
                    ->setTicket($ticket)
                    ->setCreatedAt(new DateTimeImmutable());
            }

            // on récupère le formulaire
            $solution = $form->getData();
            $manager->persist($solution);
            $manager->flush();
        }
        return $this->renderForm("solution/index.html.twig", [
            "form" => $form,
        ]);
    }
}
