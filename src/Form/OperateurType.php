<?php

namespace App\Form;

use App\Entity\Operateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulaire de création/modification d'un opérateur.
 */
class OperateurType extends AbstractType
{
    /**
     * Construit le formulaire de création/modification d'un opérateur.
     *
     * @param FormBuilderInterface $builder L'objet builder pour construire le formulaire.
     * @param array $options Les options du formulaire.
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class)
            // Ajoute un champ de formulaire de type Texte pour le nom de l'opérateur

            ->add('prenom', TextType::class)
            // Ajoute un champ de formulaire de type Texte pour le prénom de l'opérateur

            ->add('email', EmailType::class)
            // Ajoute un champ de formulaire de type Email pour l'adresse email de l'opérateur

            ->add('password', PasswordType::class)
            // Ajoute un champ de formulaire de type Password pour le mot de passe de l'opérateur

            ->add('confirmation', PasswordType::class, ['mapped' => false, 'label' => 'confirmer MDP'])
            // Ajoute un champ de formulaire de type Password pour la confirmation du mot de passe de l'opérateur
            // Le champ n'est pas mappé à l'entité Operateur ('mapped' => false)
            // L'étiquette du champ est définie comme 'confirmer MDP'

            ->add('envoyer', SubmitType::class)
            // Ajoute un bouton de soumission pour envoyer le formulaire
        ;
    }

    /**
     * Configure les options du formulaire.
     *
     * @param OptionsResolver $resolver Le resolver pour configurer les options.
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Operateur::class,
            // Spécifie que le formulaire sera lié à l'entité Operateur
        ]);
    }
}
