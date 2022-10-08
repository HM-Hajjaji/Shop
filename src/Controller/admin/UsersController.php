<?php

namespace App\Controller\admin;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

#[Route('/admin')]
class UsersController extends AbstractController
{
    private $repository;
    public function __construct(UserRepository $userRepository)
    {
        $this->repository = $userRepository;
    }

    #[Route('/users',name: 'app_users')]
    public function index():Response
    {
        $users = $this->repository->findAll();
        return $this->render('admin/users/index.html.twig',[
            'users' => $users]);
    }

}
