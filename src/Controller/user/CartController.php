<?php

namespace App\Controller\user;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/user')]
class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_user_cart_index')]
    public function index(Request $request)
    {
        $session = $request->getSession();
        $cart = $session->get('cart');
        return $this->render('user/cart/index.html.twig', ['cart' => $cart]);
    }

    #[Route('/cart/{slug}/add', name: 'app_user_cart_add', methods: ['POST'])]
    public function addToCart(Product $product, Request $request)
    {
        $session = $request->getSession();
        $mainPrice = $product->getPrice() - (($product->getPrice() * $product->getDiscount()) / 100);
        if ($session->has('cart')) {
            $detail = [
                'quantity' => $request->get('quantity'),
                'price' => $mainPrice * $request->get('quantity'),
                'date' => new \DateTime(),
                'product' => $product
            ];
            $cart = $session->get('cart');
            array_push($cart, $detail);
            $session->set('cart', $cart);
        } else {
            $detail = [
                'quantity' => $request->get('quantity'),
                'price' => $mainPrice * $request->get('quantity'),
                'date' => new \DateTime(),
                'product' => $product
            ];
            $session->set('cart', []);
            $cart = $session->get('cart');
            array_push($cart, $detail);
            $session->set('cart', $cart);
        }
        return $this->redirectToRoute('app_user_cart_index');
    }

    #[Route('/product/cart/delete/{label}', name: 'app_user_cart_delete')]
    public function delete($label, Request $request)
    {
        $session = $request->getSession();
        $cart = $session->get('cart');
        unset($cart[$label]);
        $session->set('cart', $cart);
        return $this->redirectToRoute('app_user_cart_index');
    }
}