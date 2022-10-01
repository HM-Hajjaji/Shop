<?php

namespace App\Controller\home;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(CategoryRepository $categoryRepository, ProductRepository $productRepository ,Request $request,PaginatorInterface $paginator): Response
    {
        $categories =$categoryRepository->findAll();
        $products =$productRepository->findAll();
        $products = $paginator->paginate($products,$request->query->getInt('page',1),10);
        return $this->render('home/index.html.twig',[
            'categorise' => $categories,
            'products' =>$products
        ]);
    }
}
