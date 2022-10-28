<?php

namespace App\Controller;

use App\Entity\AbstractEntities\AbstractUserClass;
use App\Entity\Operateur;
use App\Entity\Service;
use App\Entity\Ticket;
use App\Entity\User;
use App\Form\TicketType;
use App\Repository\ServiceRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class TicketController extends AbstractController
{
    #[Route('/ticket/create', name: 'app_ticket_create', methods: ['get','post'])]
    public function createTicket(ManagerRegistry $registre, Request $request): Response
    {
        // On récupère l'utilisateur connecté.
        $currentUser = $this->getUser();

        // On récupère l'entity Manager
        $manager = $registre->getManager();

        // On vient créer le formulaire du ticket, et le futur ticket.
        $ticket = new Ticket();
        $form = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);

        // Logique post submit du formulaire s'il est valide.
        if ($form->isSubmitted() && $form->isValid()) {

            // On récupère la valeur du champ 'client' dans le formulaire.
            $userEmail = $form->get('client')->getData();
            // On tente de récupérer l'utilisateur dans la BDD.
            $user = $registre->getManager()->getRepository(User::class)->findOneBy(['email' => $userEmail]);

            // Si $utilisateur n'est pas défini, c'est que l'utilisateur renseigné n'existe pas.
            if(!$user){

                // On vient ajouter l'erreur au formulaire.
                $form->get('client')->addError(new FormError("L'utilisateur renseigné n'est pas valide."));
                return $this->renderForm('ticket/createTicket/index.html.twig', [
                    'form' => $form,
                ]);
            }

            // On vient alors peupler les propriétés du ticket.
            $ticket->setClient($user);

            if($currentUser instanceof Operateur){
                $ticket->setOperateur($currentUser);
            }

            $ticket = $form->getData();

            $manager->persist($ticket);
            $manager->flush();

            return $this->redirectToRoute('LEZGO');
        }

        return $this->renderForm('ticket/createTicket/index.html.twig', [
            'form' => $form,
        ]);
//        $service = $registre->getRepository(Service::class)->findOneBy(['nom'=> $serviceName]);
//        $operateur = $registre->getRepository(Operateur::class)->find(1);
//        $user = $registre->getRepository(User::class)->findOneBy(['email'=>$email]);
//        $request->getcontent();
//
//        $ticket= new Ticket();
//        $ticket->setTitre('Ticket1')
//        ->setDescription('ticket de test')
//        ->setCreatedAt(new DateTimeImmutable())
//        ->setService($service)
//        ->setOperateur($operateur)
//        ->setClient($user);
//        $manager= $registre->getManager();
//        $manager->persist($ticket);
//        $manager->flush();
    }
}
