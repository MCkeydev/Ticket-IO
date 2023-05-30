<?php

namespace App\Form;

use App\Entity\Tache;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulaire de tâche.
 */
class TacheType extends AbstractType
{
    /**
     * Construit le formulaire de tâche.
     *
     * @param FormBuilderInterface $builder L'objet builder pour construire le formulaire.
     * @param array $options Les options du formulaire.
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add("Description", TextareaType::class, ['label_attr' => ['class' => 'custom-input-label']])
            // Ajoute un champ de formulaire de type Textarea pour le champ "Description"
            // Le tableau ['label_attr' => ['class' => 'custom-input-label']] spécifie des attributs HTML personnalisés pour l'étiquette du champ

            ->add("temps", TimeType::class, [
                "input" => "string",
            ])
            // Ajoute un champ de formulaire de type Time pour le champ "temps"
            // L'option "input" est définie comme "string" pour stocker la valeur en tant que chaîne de caractères

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
            "data_class" => Tache::class,
            // Spécifie que le formulaire sera lié à l'entité Tache
        ]);
    }
}
