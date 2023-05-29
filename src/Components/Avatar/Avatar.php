<?php

namespace App\Components\Avatar;

use App\Entity\Operateur;
use App\Entity\Technicien;
use App\Entity\User;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('avatar', template: '/components/avatar/avatar.html.twig')]
class Avatar
{
    public Technicien|Operateur|User $user;
}