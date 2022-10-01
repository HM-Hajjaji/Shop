<?php

namespace App\Controller\user;

use App\Entity\Order;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/user')]
class OrderUserController extends AbstractController
{
    #[Route('/order', name: 'app_user_order')]
    public function index(): Response
    {
        $user = $this->getUser();
        return $this->render('user/order/index.html.twig', [
            'orders' => $user->getOrders(),
        ]);
    }
    #[Route('/order/new', name: 'app_user_order_new')]
    public function new(): Response
    {
        $user = $this->getUser();
        $order = new Order();
        //
        //
        //

        $user->addOrder($order);
        return $this->redirectToRoute('app_user_order');
    }
}
