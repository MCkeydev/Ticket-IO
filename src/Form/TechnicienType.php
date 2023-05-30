<?php

namespace App\Form;

use App\Entity\Technicien;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulaire de création/modification d'un technicien.
 */
class TechnicienType extends AbstractType
{
    /**
     * Construit le formulaire de création/modification d'un technicien.
     *
     * @param FormBuilderInterface $builder L'objet builder pour construire le formulaire.
     * @param array $options Les options du formulaire.
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class)
            // Ajoute un champ de formulaire de type Texte pour le nom du technicien

            ->add('prenom', TextType::class)
            // Ajoute un champ de formulaire de type Texte pour le prénom du technicien

            ->add('email', EmailType::class)
            // Ajoute un champ de formulaire de type Email pour l'adresse email du technicien

            ->add('password', PasswordType::class)
            // Ajoute un champ de formulaire de type Password pour le mot de passe du technicien

            ->add('confirmation', PasswordType::class, ['mapped' => false, 'label' => 'confirmer mdp'])
            // Ajoute un champ de formulaire de type Password pour la confirmation du mot de passe du technicien
            // Le champ n'est pas mappé à l'entité Technicien ('mapped' => false)
            // L'étiquette du champ est définie comme 'confirmer mdp'

            ->add('service')
            // Ajoute un champ de formulaire pour le service du technicien

            ->add('Envoyer', SubmitType::class)
            // Ajoute un bouton de soumission avec l'étiquette 'Envoyer'
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
            'data_class' => Technicien::class,
            // Spécifie que le formulaire sera lié à l'entité Technicien
        ]);
    }
}
