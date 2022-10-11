<?php

namespace App\Controller\admin;

use App\Entity\User;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

#[Route('/admin')]
class UsersController extends AbstractController
{
    private $repository;
    private $paginator;
    public function __construct(UserRepository $userRepository,PaginatorInterface $paginator)
    {
        $this->repository = $userRepository;
        $this->paginator = $paginator;
    }

    #[Route('/users',name: 'app_users')]
    public function index(Request $request):Response
    {
        $users = $this->repository->findAll();
        $users = $this->paginator->paginate($users,$request->query->getInt('page',1),15);
        return $this->render('admin/users/index.html.twig',[
            'users' => $users]);
    }

    #[Route('/user/{slug}',name: 'app_admin_show_user')]
    public function show(User $user)
    {
        return $this->render('admin/users/show.html.twig',[
            'user' => $user
        ]);
    }

}
