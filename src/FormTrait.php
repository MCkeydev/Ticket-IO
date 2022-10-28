<?php

namespace App;

use App\Entity\Operateur;
use App\Entity\Technicien;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;

trait FormTrait
{
    /**
     * Vérifie tous les champs d'un formulaire, afin de voir s'il y a au moins une erreur.
     * @param FormInterface[] $formFields Tous les champs du formulaire. ($form->all())
     * @return bool retourne true si il y a au moins une erreur dans le formulaire.
     */
    public function checkErrors(array $formFields) : bool {
        foreach ($formFields as $field){
            if(count($field->getErrors()) !== 0){

                return true;
            }
        };

        return false;
    }

    /**
     * Vérifie tous les champs d'un formulaire, afin de voir s'il y a au moins une erreur.
     * @param   EntityManagerInterface $entityManager
     * @param   FormInterface $email
     * @return  bool retourne true si il existe déjà un utilisateur avec cet email.
     */
    public function checkExistingUser(EntityManagerInterface $entityManager, FormInterface $email) : bool {
        // TODO: Make this more efficient.
        if( $entityManager->getRepository(Technicien::class)->findOneBy(['email' => $email->getData()])
        || $entityManager->getRepository(Operateur::class)->findOneBy(['email' => $email->getData()])
        || $entityManager->getRepository(User::class)->findOneBy(['email' => $email->getData()])) {

            return true;
        }

        return false;
    }
}