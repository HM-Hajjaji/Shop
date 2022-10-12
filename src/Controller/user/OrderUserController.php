<?php

namespace App\Controller\user;

use App\Entity\Detail;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\DetailRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/user/order')]
class OrderUserController extends AbstractController
{
    #[Route('/',name: 'app_user_index')]
    public function index()
    {
        return $this->render('user/order/index.html.twig',['orders' => $this->getUser()->getOrders()]);
    }
    #[Route('/{slug}/show',name: 'app_user_order')]
    public function show(Order $order)
    {
        return $this->render('user/order/show.html.twig',['order' => $order]);
    }

    #[Route('/{total}/detail',name: 'app_user_order_detail')]
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
    public function new($total,$speed,$payment,Request $request,SluggerInterface $slugger,OrderRepository $orderRepository,DetailRepository $detailRepository,ProductRepository $productRepository): Response
    {
        $session = $request->getSession();
        $cart = $session->get('cart');
        $order = new Order();
        $order->setSlug($slugger->slug($this->getUser()->getName().' '.uniqid().' order shop'));
        $order->setDate(new \DateTime());
        $order->setStatus("processing");
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

    #[Route('/{slug}/edit',name: 'app_user_order_edit')]
    public function edit(Order $order)
    {
        return $this->render('user/order/edit.html.twig',[
            'order' => $order
        ]);
    }
    #[Route('/{slug}/store',name: 'app_user_order_store')]
    public function store(Order $order,Request $request,OrderRepository $orderRepository,DetailRepository $detailRepository)
    {
        $total = 0;
        foreach ($order->getListProducts() as $item)
        {
            $item->setQuantity($request->get($item->getEntityProduct()->getSlug()));
            $item->setPrice($item->getEntityProduct()->getPrice() * $request->get($item->getEntityProduct()->getSlug()));
            $total += $item->getPrice();
            $detailRepository->save($item,true);
        }
        $order->setTotal($total);
        $orderRepository->save($order,true);
        return $this->redirectToRoute('app_user_order_edit',['slug' => $order->getSlug()]);
    }
    #[Route('/{slug}/delete', name: 'app_order_delete', methods: ['POST'])]
    public function delete(Request $request, Order $order, OrderRepository $orderRepository,DetailRepository $detailRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$order->getSlug(), $request->request->get('_token')))
        {
            foreach ($order->getListProducts() as $item)
            {
               $order->removeListProduct($item);
            }
            $orderRepository->remove($order, true);
        }
        return $this->redirectToRoute('app_user_index');
    }

    #[Route('/{slug}/delete-product/{id}', name: 'app_order_delete_product')]
    #[ParamConverter('order',options: ['mapping' => ['slug' => 'slug']])]
    #[ParamConverter('detail',options: ['mapping' => ['id' => 'id']])]
    public function deleteProduct(Order $order,Detail $detail,DetailRepository $detailRepository,OrderRepository $orderRepository): Response
    {
        $detailRepository->remove($detail,true);
        if ($order->getListProducts()->count() == 0)
        {
            $orderRepository->remove($order,true);
        }
        return $this->redirectToRoute('app_user_order_edit',['slug' => $order->getSlug()]);
    }
}
