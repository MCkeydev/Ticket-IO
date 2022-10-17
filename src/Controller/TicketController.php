<?php

namespace App\Controller;

use App\Entity\Operateur;
use App\Entity\Service;
use App\Entity\Ticket;
use App\Entity\User;
use App\Repository\ServiceRepository;
use DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class TicketController extends AbstractController
{
    #[Route('/ticket/create', name: 'app_ticket_create', methods: ['get','post'])]
    public function createTicket(ManagerRegistry $registre, SerializerInterface $serializer): Response
    {
        $service = $registre->getRepository(Service::class)->findOneBy(['nom'=>'Test']);
        $operateur = $registre->getRepository(Operateur::class)->find(1);
        $user = $registre->getRepository(User::class)->findOneBy(['email'=>'f@f.fr']);
        $ticket= new Ticket();
        $ticket->setTitre('Ticket1')
        ->setDescription('ticket de test')
        ->setCreatedAt(new DateTimeImmutable())
        ->setService($service)
        ->setOperateur($operateur)
        ->setClient($user);
        $manager= $registre->getManager();
        $manager->persist($ticket);
        $manager->flush();
        return new Response($serializer->serialize($ticket,'json',[AbstractObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function($param, $param2){
            return $param->getId();
        }]), Response::HTTP_OK );
    }
}
