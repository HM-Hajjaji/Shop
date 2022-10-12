<?php

namespace App\Controller\admin;

use App\Entity\Order;
use App\Form\OrderType;
use App\Repository\OrderRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class OrderController extends AbstractController
{

    private $repository;
    private $paginator;
    public function __construct(OrderRepository $orderRepository,PaginatorInterface $paginator)
    {
        $this->repository = $orderRepository;
        $this->paginator = $paginator;
    }

    #[Route('/order/{status?}', name: 'app_order_index', methods: ['GET'],defaults: ['status' => 'all'])]
    public function index(Request $request,$status): Response
    {
        if ($status != "all")
        {
            $orders = $this->repository->findBy(['status' => $status],['date' => 'DESC']);
            $orders = $this->paginator->paginate($orders,$request->query->getInt('page',1),5);
        }
        else{
            $orders = $this->repository->findBy([],['date' => 'DESC']);
            $orders = $this->paginator->paginate($orders,$request->query->getInt('page',1),5);
        }
        return $this->render('admin/order/index.html.twig', [
            'orders' =>$orders,
        ]);
    }

    #[Route('/{slug}/show', name: 'app_order_show')]
    public function show(Order $order,Request $request,OrderRepository $orderRepository): Response
    {
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $orderRepository->save($order, true);

            return $this->redirectToRoute('app_order_show', ['slug' => $order->getSlug()]);
        }

        return $this->renderForm('admin/order/show.html.twig', [
            'order' => $order,
            'form' => $form
        ]);
    }
}
