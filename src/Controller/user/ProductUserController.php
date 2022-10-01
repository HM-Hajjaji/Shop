<?php

namespace App\Controller\user;

use App\Entity\Detail;
use App\Entity\Order;
use App\Entity\OrderDetail;
use App\Entity\Product;
use App\Repository\DetailRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Egulias\EmailValidator\Result\Reason\DetailedReason;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/user')]
class ProductUserController extends AbstractController
{
    #[Route('/product/{slug}', name: 'app_user_product')]
    public function show(Product $product): Response
    {
        return $this->render('user/product/index.html.twig', [
            'product' => $product
        ]);
    }

    #[Route('/product/cart/show', name: 'app_user_product_cart')]
    public function cart(Request $request)
    {
        $session = $request->getSession();
        $cart = $session->get('cart');
        return $this->render('user/product/cart.html.twig', ['cart' => $cart]);
    }

    #[Route('/product/cart/delete/{label}', name: 'app_user_product_cart_delete')]
    public function delete($label, Request $request)
    {
        $session = $request->getSession();
        $cart = $session->get('cart');
        unset($cart[$label]);
        $session->set('cart', $cart);
        return $this->redirectToRoute('app_user_product_cart');
    }

    #[Route('/product/cart/{slug}', name: 'app_user_product_cart_add', methods: ['POST'])]
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
        return $this->redirectToRoute('app_user_product_cart');
    }

    #[Route('/shop/{total}', name: 'app_user_shop')]
    public function shop($total,Request $request,OrderRepository $orderRepository,DetailRepository $detailRepository,ProductRepository $productRepository): Response
    {
        $session = $request->getSession();
        $cart = $session->get('cart');
        $order = new Order();
        $order->setSlug($this->getUser()->getName().' '.uniqid().' order shop');
        $order->setDate(new \DateTime());
        $order->setStatus("on the way");
        $order->setDelivery(1.5);
        $order->setTotal($total + $order->getDelivery());
        $order->setEntityUser($this->getUser());
        $product = null;
        foreach ( $cart as $item)
        {
            $product = $item['product'];
            $detail = new Detail();
            $detail->setDate($item['date']);
            $detail->setPrice($item['price']);
            $detail->setQuantity($item['quantity']);
            $order->addListProduct($detail);
            $detail->setEntityProduct($product);
/*//            $product->addListOrder($detail);
            $orderRepository->save($order,true);*/
            $detailRepository->save($detail,true);
        }
        $cart = [];
        return $this->redirectToRoute('app_home');
    }
}
