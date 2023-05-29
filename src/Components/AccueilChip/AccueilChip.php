<?php
namespace App\Components\AccueilChip;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[
    AsTwigComponent(
        "accueilChip",
        template: "components\accueilChip\accueilChip.html.twig"
    )
]
class AccueilChip
{
    public string $type = "nouveau";
}
