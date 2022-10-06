<?php

namespace App\Controller\user;

use App\Entity\Detail;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\DetailRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/user')]
class OrderUserController extends AbstractController
{
    #[Route('/order',name: 'app_user_index')]
    public function index()
    {
        return $this->render('user/order/index.html.twig',['orders' => $this->getUser()->getOrders()]);
    }
    #[Route('/order/{slug}',name: 'app_user_order')]
    public function show(Order $order)
    {
        return $this->render('user/order/show.html.twig',['order' => $order]);
    }

    #[Route('/order/{total}/detail',name: 'app_user_order_detail')]
    public function detail($total,Request $request)
    {
        $session = $request->getSession();
        $cart = $session->get('cart');
        $speed = 0;
        if ($request->get('speed') == 'fast')
        {
            $speed = 1.75;
        }
        elseif ($request->get('speed') == 'average')
        {
            $speed = 1.25;
        }
        else{
            $speed = 0.75;
        }

        if ($request->get('payment') == 'cart')
        {
            $payment = 'cart';
        }
        else{
            $payment = 'shipping';
        }
        return $this->render('user/order/detail.html.twig',['total' => $total,'speed' => $speed,'payment' => $payment,'cart' => $cart]);
    }

    #[Route('/new/{total}/{speed}/{payment}', name: 'app_user_order_new')]
    public function new($total,$speed,$payment,Request $request,OrderRepository $orderRepository,DetailRepository $detailRepository,ProductRepository $productRepository): Response
    {
        $session = $request->getSession();
        $cart = $session->get('cart');
        $order = new Order();
        $order->setSlug($this->getUser()->getName().' '.uniqid().' order shop');
        $order->setDate(new \DateTime());
        $order->setStatus("on the way");
        $order->setDelivery($speed);
        $order->setPaymentMethods($payment);
        if ($order->getDelivery() == 1.75)
        {
            $order->setDeliverySpeed('fast');
        }
        elseif($order->getDelivery() == 1.25){

            $order->setDeliverySpeed('average');
        }
        else{
            $order->setDeliverySpeed('slow');
        }
        $order->setTotal($total);
        $order->setEntityUser($this->getUser());

        foreach ( $cart as $item)
        {
            $product = $productRepository->find($item['product']->getId());
            $detail = new Detail();
            $detail->setDate($item['date']);
            $detail->setPrice($item['price']);
            $detail->setQuantity($item['quantity']);
            $product->setQuantity($product->getQuantity() -  $detail->getQuantity());
            $detail->setEntityOrder($order);
            $detail->setEntityProduct($product);
            $detailRepository->save($detail,true);
            $productRepository->save($product,true);
        }
        $orderRepository->save($order,true);

        $cart = [];
        $session->set('cart',$cart);
        return $this->redirectToRoute('app_user_index');
    }
}
