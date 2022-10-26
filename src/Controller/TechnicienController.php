<?php

namespace App\Controller;

use App\Entity\Service;
use App\Entity\Technicien;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class TechnicienController extends AbstractController
{
    #[Route('/technicien/create', name: 'app_technicien_create',methods:['POST'])]
    public function createTechnicien(ManagerRegistry $registre, Request $request,UserPasswordHasherInterface $hasher): Response
    {
        $email = $request->get('email');
        $service = $registre->getRepository(Service::class)->findOneBy(['nom'=>$request->get('service')]);
        $password = $request->get('mdp');

        $technicien = new Technicien();
        $technicien->setEmail($email)
        ->setService($service)
        ->setPassword($hasher->hashPassword($technicien, $password));

        $manager = $registre->getManager();
        $manager->persist($technicien);
        $manager->flush();

        return $this->render('technicien/index.html.twig', [
            'controller_name' => 'TechnicienController',
        ]);
    }

    #[Route('/technicien/delete', name: 'app_technicien_delete',methods:['DELETE'])]
    public function deleteTechnicien(ManagerRegistry $registre, SerializerInterface $serializer, Request $request): Response
    {
        try{  
            $technicienRepository = $registre->getRepository(Technicien::class);
            $technicien = $technicienRepository->find(2);
            if(!$technicien){
                throw new EntityNotFoundException("Le compte n'existe pas");
            }
        $technicienRepository->remove($technicien, true);
        return new Response();

        }catch(\Exception $exception){
            return new Response ($exception->getMessage());
        }
    }

    #[Route('/technicien/update/{id}', name: 'app_technicien_update',methods:['PUT', 'POST'])]
    public function updateTechnicien(ManagerRegistry $registre, Request $request,UserPasswordHasherInterface $hasher, int $id): Response
    {
        // TODO : convertir en JSON
        $manager = $registre->getManager();
            $technicien = $manager->getRepository(Technicien::class)->find($id);
            if(!$technicien){
                throw new EntityNotFoundException("Le technicien n'existe pas");
            }   
            $service = $registre->getRepository(Service::class)->findOneBy(['nom'=>$request->get('service')]);
            $technicien->setEmail($request->get('email'))
            ->setService($service)
            ->setPassword($hasher->hashPassword($technicien, $request->get("mdp")));
            $manager->flush();
            return new Response($request->getContent());
        }
}