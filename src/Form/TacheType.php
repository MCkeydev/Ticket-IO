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

class TacheType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder
            ->add("Description", TextareaType::class, ['label_attr' => ['class' => 'custom-input-label']])
            ->add("temps", TimeType::class, [
                "input" => "string",
            ])
            ->add("envoyer", SubmitType::class, ['attr' => ['class' => 'button']]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            "data_class" => Tache::class,
        ]);
    }
}
