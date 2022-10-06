<?php

namespace App\Controller\home;

use App\Entity\Category;
use App\Entity\Product;
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
        $products =$productRepository->findBy([],['date' => 'DESC']);
        $products = $paginator->paginate($products,$request->query->getInt('page',1),10);
        return $this->render('home/index.html.twig',[
            'categorise' => $categories,
            'products' =>$products
        ]);
    }
    #[Route('/category/{slug}', name: 'app_home_filter')]
    public function filter(Category $category,CategoryRepository $categoryRepository,Request $request,PaginatorInterface $paginator): Response
    {
        $categories =$categoryRepository->findAll();
        $products =$category->getProducts();
        $products = $paginator->paginate($products,$request->query->getInt('page',1),10);
        return $this->render('home/index.html.twig',[
            'products' =>$products,
            'categorise' => $categories,
        ]);
    }

    #[Route('/product/{slug}', name: 'app_product_show')]
    public function show(Product $product): Response
    {
        return $this->render('user/product/show.html.twig', [
            'product' => $product
        ]);
    }

}
