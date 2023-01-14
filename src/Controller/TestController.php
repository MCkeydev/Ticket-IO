<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class TestController extends AbstractController
{
    #[Route('/test', name: 'app_test')]
    public function index(): Response
    {

        return $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }

    #[Route('/test/get', name: 'get_ticket', methods: ['GET'])]
    public function test(EntityManagerInterface $manager, SerializerInterface $serializer): Response
    {
        $tickets = $manager->getRepository(Ticket::class)->findUserTickets(2);
        $result = [];
        foreach ($tickets['results'] as $ticket) {
            $result[] = $ticket->getTitre();
        }

        return new Response($serializer->serialize($tickets, 'json'));
    }
}
