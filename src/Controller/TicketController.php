<?php

namespace App\Controller;

use App\Entity\Operateur;
use App\Entity\Service;
use App\Entity\Ticket;
use App\Entity\User;
use App\Entity\Criticite;
use App\Entity\Gravite;
use App\Entity\Status;
use App\Repository\ServiceRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class TicketController extends AbstractController
{
    #[Route('/ticket/create', name: 'app_ticket_create', methods: ['post'])]
    public function createTicket(ManagerRegistry $registre, SerializerInterface $serializer, Request $request): Response
    {
            // Récupération des objets depuis la BDD
        $service = $registre->getRepository(Service::class)->findOneBy(['nom'=>$request->get('service')]);
        $operateur = $registre->getRepository(Operateur::class)->find(1);
        $user = $registre->getRepository(User::class)->findOneBy(['email'=>$request->get('client')]);
        $criticite = $registre->getRepository(Criticite::class)->findOneBy(['libelle'=>$request->get('criticite')]);
        $gravite = $registre->getRepository(Gravite::class)->findOneBy(['libelle'=>$request->get('gravite')]);
        $status = $registre->getRepository(Status::class)->findOneBy(['libelle'=>$request->get('status')]);
        
            // Création du Ticket avec les différents objets récupérés.
        $ticket= new Ticket();
        $ticket->setTitre('Ticket1')
        ->setDescription('ticket de test')
        ->setCreatedAt(new DateTimeImmutable())
        ->setService($service)
        ->setOperateur($operateur)
        ->setClient($user)
        ->setStatus($status)
        ->setGravite($gravite)
        ->setCriticite($criticite);

            // Envoi de l'objet dans la BDD.
        $manager= $registre->getManager();
        $manager->persist($ticket);
        $manager->flush();
         return new Response($serializer->serialize($ticket,'json',[AbstractObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function($param, $param2){
         return $param->getId();
         }]), Response::HTTP_OK );
    }
    #[Route('/ticket/delete', name: 'app_ticket_delete', methods: ['get','post'])]
    public function deleteTicket(ManagerRegistry $registre, SerializerInterface $serializer, Request $request): Response
    {
        try{  
            $ticketRepository = $registre->getRepository(Ticket::class);
            $ticket=$ticketRepository->find(4);
            if(!$ticket){
                throw new EntityNotFoundException("Le ticket n'existe pas");
            }
        $ticketRepository->remove($ticket, true);
        return new Response();

        }catch(\Exception $exception){
            return new Response ($exception->getMessage());
        }
    }

    #[Route('/ticket/update/{id}', name: 'app_ticket_update', methods: ['get','post'])]
    public function updateTicket (ManagerRegistry $registre, SerializerInterface $serialize, Request $request, int $id): Response
    {
        try{  
            $manager = $registre->getManager();
            $ticket = $manager->getRepository(Ticket::class)->find($id);
            if(!$ticket){
                throw new EntityNotFoundException('Le ticket' .$id. "n'existe pas");
            }   
            $service = $registre->getRepository(Service::class)->findOneBy(['nom'=>$request->get('service')]);
            $operateur = $registre->getRepository(Operateur::class)->find(1);
            $user = $registre->getRepository(User::class)->findOneBy(['email'=>$request->get('client')]);
            $criticite = $registre->getRepository(Criticite::class)->findOneBy(['libelle'=>$request->get('criticite')]);
            $gravite = $registre->getRepository(Gravite::class)->findOneBy(['libelle'=>$request->get('gravite')]);
            $status = $registre->getRepository(Status::class)->findOneBy(['libelle'=>$request->get('status')]);
            $ticket->setTitre('Ticket1')
            ->setDescription('ticket de test')
            ->setService($service)
            ->setOperateur($operateur)
            ->setClient($user)
            ->setStatus($status)
            ->setGravite($gravite)
            ->setCriticite($criticite);
            $manager->flush();
            return $this->redirectToRoute('app_login', [ 'id' => $ticket->getId() ]);
        }catch(\Exception $exception){
            return new Response ($exception->getMessage());
        }
    }
}
