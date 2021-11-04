<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Security\Core\Security;

class MeController
{
    private $security;

    public function __construct(Security $security )
    {
        $this->security = $security;
    }


    public function __invoke()
    {
        $user = $this->security->getUser();
        return $user;
    }
}