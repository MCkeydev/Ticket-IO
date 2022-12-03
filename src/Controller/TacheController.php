<?php

namespace App\Controller;

use App\Entity\Tache;
use App\Entity\Technicien;
use App\Entity\Ticket;
use App\Form\TacheType;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TacheController extends AbstractController
{
    #[
        Route(
            "/tache/create/{id}",
            name: "app_tache_create",
            methods: ["get", "post"]
        )
    ]
    public function createTache(
        Ticket $ticket,
        EntityManagerInterface $manager,
        Request $request
    ): Response {
        $this->denyAccessUnlessGranted("ROLE_TECHNICIEN");
        // on récupère lm'utilisateur connecté
        $currentUser = $this->getUser();
        // On vient créer le formulaire de la tache, et la future tache.
        $tache = new Tache();
        $form = $this->createForm(TacheType::class, $tache);
        $form->handlerequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($currentUser instanceof Technicien) {
                $tache
                    ->setAuteur($currentUser)
                    ->setTicket($ticket)
                    ->setCreatedAt(new DateTimeImmutable());
            }
            // on récupère le formulaire
            $tache = $form->getData();
            $manager->persist($tache);
            $manager->flush();
        }
        return $this->renderForm("tache/index.html.twig", [
            "form" => $form,
        ]);
    }
}
