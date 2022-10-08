<?php

namespace App\Controller\admin;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('admin/category')]
class CategoryController extends AbstractController
{

    private $repository;
    private $paginator;
    public function __construct(CategoryRepository $categoryRepository,PaginatorInterface $paginator)
    {
        $this->repository = $categoryRepository;
        $this->paginator = $paginator;
    }

    #[Route('/', name: 'app_category_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $categories = $this->repository->findAll();
        $categories = $this->paginator->paginate($categories,$request->query->getInt('page',1),5);
        return $this->render('admin/category/index.html.twig', [
            'categories' => $categories,
        ]);
    }

/*    #[Route('/product-new/{slug}', name: 'app_category_index', methods: ['GET'])]
    public function new_product(Request $request,Category $category): Response
    {
        return $this->render('admin/category/show.html.twig', [

        ]);
    }*/

    #[Route('/new', name: 'app_category_new', methods: ['GET', 'POST'])]
    public function new(SluggerInterface $slugger,Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category,['btn_name' => "Create",'action' => $this->generateUrl('app_category_new'),]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $category->setDate(new \DateTime());
            $category->setSlug($slugger->slug($category->getName()." ".uniqid().' title','-'));
            $this->repository->save($category, true);
            return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/category/new.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}/show', name: 'app_category_show', methods: ['GET'])]
    public function show(Category $category,Request $request): Response
    {
        $products = $category->getProducts();
        $products = $this->paginator->paginate($products,$request->query->getInt('page',1),2);
        return $this->render('admin/category/show.html.twig', [
            'category' => $category,
            'products' => $products
        ]);
    }

    #[Route('/{slug}/edit', name: 'app_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Category $category, CategoryRepository $categoryRepository): Response
    {
        $form = $this->createForm(CategoryType::class, $category,['btn_name' => 'Save','action' => $this->generateUrl('app_category_edit',['slug' => $category->getSlug()])]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $categoryRepository->save($category, true);
            return $this->redirectToRoute('app_category_index');
        }
        $products = $category->getProducts();
        $products = $this->paginator->paginate($products,$request->query->getInt('page',1),2);
        return $this->renderForm('admin/category/edit.html.twig',[
            'category' => $category,
            'form' => $form,
            'products' => $products,
            'modal_id' => str_replace(' ','',$category->getSlug())
        ]);
    }

    #[Route('/{slug}/delete', name: 'app_category_delete', methods: ['POST'])]
    public function delete(Request $request, Category $category, CategoryRepository $categoryRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getSlug(), $request->request->get('_token'))) {
            $categoryRepository->remove($category, true);
        }

        return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
    }
}
