<?php

namespace App\Controller;

use App\Entity\Operateur;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class OperateurController extends AbstractController
{
    #[Route('/operateur/create', name: 'app_operateur_create', methods:('POST'))]
    public function createOperateur(ManagerRegistry $registre, Request $request,UserPasswordHasherInterface $hasher): Response
    {
        $email = $request->get('email');
        $password = $request->get('mdp');

        $operateur = new Operateur();
        $operateur->setEmail($email)
        ->setPassword($hasher->hashPassword($operateur, $password));
        $manager = $registre->getManager();
        $manager->persist($operateur);
        $manager->flush();

        return $this->render('operateur/index.html.twig', [
            'controller_name' => 'OperateurController',
        ]);
    }
    
    #[Route('/operateur/delete', name: 'app_operateur_delete',methods:['DELETE'])]
    public function deleteOperateur(ManagerRegistry $registre): Response
    {
        try{  
            $this->denyAccessUnlessGranted('ROLE_OPERATEUR'); // pour s'assurer que c'est bien un opérateur.

            $operateurRepository = $registre->getRepository(Operateur::class);
            $operateur = $operateurRepository->find(3);
            if(!$operateur){
                throw new EntityNotFoundException("Le compte n'existe pas");
            }
        $operateurRepository->remove($operateur, true);

        return new Response();

        }catch(\Exception $exception){
            return new Response ($exception->getMessage());
        }
    }
    #[Route('/operateur/update/{id}', name: 'app_operateur_update',methods:['PUT', 'POST'])]
    public function updateOperateur(ManagerRegistry $registre, Request $request,UserPasswordHasherInterface $hasher, int $id): Response
    {
        //TODO : convertir en JSON
        try{  
            $this->denyAccessUnlessGranted('ROLE_OPERATEUR'); // pour s'assurer que c'est bien un opérateur.

            $manager = $registre->getManager();
            $operateur = $manager->getRepository(Operateur::class)->find($id);
            if(!$operateur){
                throw new EntityNotFoundException("Le compte n'existe pas");
            }
        $operateur->setEmail($request->get('email'))
        ->setPassword($hasher->hashPassword($operateur, $request->get("mdp")));
        $manager->flush();
        return new Response();

        }catch(\Exception $exception){
            return new Response ($exception->getMessage());
        }
    }
}
