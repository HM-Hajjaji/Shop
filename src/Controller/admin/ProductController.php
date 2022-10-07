<?php

namespace App\Controller\admin;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\service\FileUploader;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('admin/product')]
class ProductController extends AbstractController
{
    private $repository;
    private $paginator;
    private $upload;
    public function __construct(ProductRepository $productRepository,PaginatorInterface $paginator,FileUploader $upload)
    {
        $this->repository = $productRepository;
        $this->paginator = $paginator;
        $this->upload = $upload;
    }


    #[Route('/', name: 'app_product_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $products = $this->repository->findAll();
        $products = $this->paginator->paginate($products,$request->query->getInt('page',1),5);
        return $this->render('admin/product/index.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product,['btn_name' => 'Create','action' => $this->generateUrl('app_product_new')]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $product->setDate(new \DateTime());
            $product->setSlug($product->getName().' '.uniqid().' '.'title');
            $product->setPhoto($this->upload->upload($form->get('photo')->getData(),$this->getParameter('upload_directory'),'product'));
            $this->repository->save($product, true);
            return $this->redirectToRoute('app_product_show', ['slug' => $product->getSlug()], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('admin/product/new.html.twig', [
            'product' => $product,
            'form' => $form,
            'modal_id' => 'productNew'
        ]);
    }

    #[Route('/{slug}/show', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('admin/product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{slug}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        $photo = $product->getPhoto();
        $form = $this->createForm(ProductType::class, $product,['btn_name' => 'Save','photo_require' => false,'action' => $this->generateUrl('app_product_edit',['slug' => $product->getSlug()])] );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($request->files->get('product')['photo'])
            {
                $product->setPhoto($this->upload->update($form->get('photo')->getData(),$this->getParameter('upload_directory'),'product',$photo));
            }
            $productRepository->save($product, true);
            return $this->redirectToRoute('app_product_show', ['slug' => $product->getSlug()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}/delete', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getSlug(), $request->request->get('_token'))) {
            $this->upload->remove($product->getPhoto());
            $productRepository->remove($product, true);
        }
        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }
}
