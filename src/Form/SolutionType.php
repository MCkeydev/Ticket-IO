<?php

namespace App\Form;

use App\Entity\Solution;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulaire de solution.
 */
class SolutionType extends AbstractType
{
    /**
     * Construit le formulaire de solution.
     *
     * @param FormBuilderInterface $builder L'objet builder pour construire le formulaire.
     * @param array $options Les options du formulaire.
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add("solution", TextareaType::class, ['label_attr' => ['class' => 'custom-input-label']])
            // Ajoute un champ de formulaire de type Textarea pour le champ "solution"
            // Le tableau ['label_attr' => ['class' => 'custom-input-label']] spécifie des attributs HTML personnalisés pour l'étiquette du champ

            ->add("envoyer", SubmitType::class, ['attr' => ['class' => 'button']]);
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
            "data_class" => Solution::class,
            // Spécifie que le formulaire sera lié à l'entité Solution
        ]);
    }
}
