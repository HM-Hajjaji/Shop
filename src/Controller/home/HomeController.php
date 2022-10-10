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
    public function index(ProductRepository $productRepository): Response
    {
        $products =$productRepository->findBy([],['date' => 'DESC'],10);
        return $this->render('home/index.html.twig',[
            'products' =>$products,
        ]);
    }
    #[Route('/shop', name: 'app_shop')]
    public function shop(CategoryRepository $categoryRepository, ProductRepository $productRepository ,Request $request,PaginatorInterface $paginator): Response
    {
        $categories =$categoryRepository->findAll();
        $products =$productRepository->findBy([],['date' => 'DESC']);
        $products = $paginator->paginate($products,$request->query->getInt('page',1),10);
        return $this->render('home/shop.html.twig',[
            'categorise' => $categories,
            'products' =>$products,
            'name' => ''
        ]);
    }

    #[Route('/shop/search', name: 'app_search')]
    public function search(CategoryRepository $categoryRepository, ProductRepository $productRepository ,Request $request,PaginatorInterface $paginator): Response
    {
        if($request->get('search') == null )
        {
            return $this->redirectToRoute('app_shop');
        }
        $categories =$categoryRepository->findAll();
        $products =$productRepository->search($request->get('search'));
        $products = $paginator->paginate($products,$request->query->getInt('page',1),10);
        return $this->render('home/shop.html.twig',[
            'categorise' => $categories,
            'products' =>$products,
            'name' => ''
        ]);
    }

    #[Route('/category/{slug}', name: 'app_home_filter')]
    public function filter(Category $category,CategoryRepository $categoryRepository,Request $request,PaginatorInterface $paginator): Response
    {
        $categories =$categoryRepository->findAll();
        $products =$category->getProducts();
        $products = $paginator->paginate($products,$request->query->getInt('page',1),10);
        return $this->render('home/shop.html.twig',[
            'products' =>$products,
            'categorise' => $categories,
            'name' => $category->getName()
        ]);
    }

    #[Route('/product/{slug}', name: 'app_product_user_show')]
    public function show(Product $product,ProductRepository $productRepository): Response
    {
        $category = $product->getCategory();
        $products = $productRepository->findBy(['category' => $category],['date' => 'DESC'],10);
        return $this->render('user/product/show.html.twig', [
            'product' => $product,
            'products' => $products,
            'category' => $category
        ]);
    }

    #[Route('/contact', name: 'app_contact')]
    public function contact(): Response
    {
        return $this->render('home/contact.html.twig');
    }
}
