<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user/create', name: 'app_user_create')]
    public function createUser(ManagerRegistry $registre, Request $request, UserPasswordHasherInterface $hasher): Response
    {
        $email = $request->get('email');
        $password = $request->get('mdp');

        $user = new User();
        $user ->setEmail($email)
        ->setPassword($hasher->hashPassword($user, $password));

        $manager = $registre->getManager();
        $manager->persist($user);
        $manager->flush();

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/user/delete', name: 'app_user_delete',methods:['DELETE'])]
    public function deleteUser(ManagerRegistry $registre): Response
    {
        try {
            $userRepository = $registre->getRepository(User::class);
            $user = $userRepository->find(2);
            if (!$user) {
                throw new EntityNotFoundException("Le compte n'existe pas");
            }
        $userRepository->remove($user, true);
        return new Response();

        } catch (\Exception $exception) {
            return new Response($exception->getMessage());
        }
    }

    #[Route('/user/update/{id}', name: 'app_user_update')]
    public function updateUser(ManagerRegistry $registre, Request $request, UserPasswordHasherInterface $hasher, int $id): Response
    {
         // TODO : convertir en JSON
         $manager = $registre->getManager();
         $user = $manager->getRepository(User::class)->find($id);
         if (!$user) {
             throw new EntityNotFoundException("L'utilisateur n'existe pas");
         }
         $user->setEmail($request->get('email'))
         ->setPassword($hasher->hashPassword($user, $request->get("mdp")));
         $manager->flush();
         return new Response($request->getContent());
     }
}
