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
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder
            ->add("titre")
            ->add("description")
            ->add("service")
            ->add("client", EmailType::class, [
                "mapped" => false,
                "data" => $options["client_email"],
            ])
            ->add("status")
            ->add("criticite")
            ->add("gravite");

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (
            FormEvent $event
        ) {
            $form = $event->getForm();

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
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            "data_class" => Ticket::class,
            "client_email" => "",
            "justify" => false,
        ]);
        $resolver->setAllowedTypes("client_email", "string");
        $resolver->setAllowedTypes("justify", "bool");
    }
}
