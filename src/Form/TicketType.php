<?php

namespace App\Form;

use App\Entity\Ticket;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add("titre")
			->add("description")
			->add("service")
			->add("client", EmailType::class, [
				"mapped" => false,
				"data" => $options["client_email"],
			])
			->add("technicien")
			->add("status")
			->add("criticite")
			->add("gravite")
			->add("Valider", SubmitType::class);
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			"data_class" => Ticket::class,
			"client_email" => "",
		]);
		$resolver->setAllowedTypes("client_email", "string");
	}
}
