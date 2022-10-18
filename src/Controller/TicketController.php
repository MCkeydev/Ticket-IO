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
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class TicketController extends AbstractController
{
    #[Route('/ticket/create', name: 'app_ticket_create', methods: ['get','post'])]
    public function createTicket(ManagerRegistry $registre, SerializerInterface $serializer, Request $request): Response
    {

    }
}
