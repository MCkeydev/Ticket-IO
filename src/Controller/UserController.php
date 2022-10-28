<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormError;
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
        $manager = $registre->getManager();
        $user = new User();
        // $user ->setEmail($email)
        // ->setPassword($hasher->hashPassword($user, $password));
        
        $form =  $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            if($form->get('password')->getData() == $form->get('confirmation')->getData()){

                $user->setPassword($hasher->hashPassword($user, $form->get('password')->getData()));
                $manager->persist($user);
                $manager->flush();
            }
            else{
                $form->get('password')->addError(new FormError('les 2 mdp ne sont pas identiques'));

                return $this->renderForm('user/index.html.twig', [
                    'form' => $form,
                ]);
            }

            return $this->redirectToRoute('task_success');
        }

        return $this->renderForm('user/index.html.twig', [
            'form' => $form,
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
