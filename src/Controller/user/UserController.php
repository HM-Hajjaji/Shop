<?php

namespace App\Controller\user;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/profile', name: 'app_user_profile')]
    public function profile(): Response
    {
        return $this->render('user/profile/index.html.twig');
    }
}
