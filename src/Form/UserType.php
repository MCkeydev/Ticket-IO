<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulaire de création/modification d'un utilisateur.
 */
class UserType extends AbstractType
{
    /**
     * Construit le formulaire de création/modification d'un utilisateur.
     *
     * @param FormBuilderInterface $builder L'objet builder pour construire le formulaire.
     * @param array $options Les options du formulaire.
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add("email", EmailType::class)
            // Ajoute un champ de formulaire de type Email pour l'adresse email de l'utilisateur

            ->add("nom", TextType::class)
            // Ajoute un champ de formulaire de type Texte pour le nom de l'utilisateur

            ->add("prenom", TextType::class)
            // Ajoute un champ de formulaire de type Texte pour le prénom de l'utilisateur

            ->add("Mot de passe", PasswordType::class)
            // Ajoute un champ de formulaire de type Password pour le mot de passe de l'utilisateur

            ->add("confirmation", PasswordType::class, [
                "mapped" => false,
                "label" => "Confirmer le mot de passe",
            ])
            // Ajoute un champ de formulaire de type Password pour la confirmation du mot de passe de l'utilisateur
            // Le champ n'est pas mappé à l'entité User ('mapped' => false)
            // L'étiquette du champ est définie comme 'confirmer mdp'

            ->add("Envoyer", SubmitType::class, ["attr" => ["class" => "button"]]);
        // Ajoute un bouton de soumission avec la classe CSS "button"
    }

    /**
     * Configure les options du formulaire.
     *
     * @param OptionsResolver $resolver Le resolver pour configurer les options.
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            "data_class" => User::class,
            // Spécifie que le formulaire sera lié à l'entité User
        ]);
    }
}
