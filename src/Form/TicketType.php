<?php

namespace App\Form;

use App\Entity\Ticket;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulaire de création/modification d'un ticket.
 */
class TicketType extends AbstractType
{
    /**
     * Construit le formulaire de création/modification d'un ticket.
     *
     * @param FormBuilderInterface $builder L'objet builder pour construire le formulaire.
     * @param array $options Les options du formulaire.
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add("titre")
            // Ajoute un champ de formulaire pour le titre du ticket

            ->add("description")
            // Ajoute un champ de formulaire pour la description du ticket

            ->add("service")
            // Ajoute un champ de formulaire pour le service du ticket

            ->add("client", EmailType::class, [
                "mapped" => false,
                "data" => $options["client_email"],
            ])
            // Ajoute un champ de formulaire de type Email pour l'adresse email du client
            // Le champ n'est pas mappé à l'entité Ticket ('mapped' => false)
            // La valeur par défaut du champ est définie à partir de l'option "client_email"

            ->add("status")
            // Ajoute un champ de formulaire pour le statut du ticket

            ->add("criticite")
            // Ajoute un champ de formulaire pour la criticité du ticket

            ->add("gravite");
        // Ajoute un champ de formulaire pour la gravité du ticket

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (
            FormEvent $event
        ) {
            $form = $event->getForm();

            if ($form->getConfig()->getOption('justify') === false) {
                return;
            };

            if (
                strval(
                    $form
                        ->get("service")
                        ->getData()
                        ->getId()
                ) !== $event->getData()["service"]
            ) {
                $form->add("justify", TextareaType::class, [
                    "mapped" => false,
                    "label" => "Veuillez justifier le reroutage du ticket",
                ]);
            }
            $form->createView();
        });

        $builder->add("Valider", SubmitType::class, ["attr" => ["class" => "button"]]);
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
            "data_class" => Ticket::class,
            // Spécifie que le formulaire sera lié à l'entité Ticket
            "client_email" => "",
            // Définit une option "client_email" avec une valeur par défaut de chaîne vide
            "justify" => false,
            // Définit une option "justify" avec une valeur par défaut de false
        ]);

        $resolver->setAllowedTypes("client_email", "string");
        // Spécifie que l'option "client_email" doit être de type string

        $resolver->setAllowedTypes("justify", "bool");
        // Spécifie que l'option "justify" doit être de type bool
    }
}
