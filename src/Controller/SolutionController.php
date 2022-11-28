<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SolutionController extends AbstractController
{
    #[Route('/solution/create', name: 'app_ticket_create', methods: ['get','post'])]
    public function createSolution(Request $request) {

    }
}