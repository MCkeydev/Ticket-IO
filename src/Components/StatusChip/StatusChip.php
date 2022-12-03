<?php

namespace App\Components\StatusChip;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('statusChip', template: 'components/statusChip/statusChip.html.twig')]
class StatusChip
{
    public string $type = 'ticket';
}