<?php

namespace App\Controller\admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }
    #[Route('/profile', name: 'app_admin_profile')]
    public function profile(): Response
    {
        return $this->render('admin/profile/index.html.twig');
    }
}
