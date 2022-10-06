<?php

namespace App\Controller\admin;

use App\Entity\Profile;
use App\Form\ChangePassType;
use App\Form\ProfileType;
use App\Repository\CategoryRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(ChartBuilderInterface $chartBuilder,OrderRepository $orderRepository,CategoryRepository $categoryRepository,UserRepository $userRepository,ProductRepository $productRepository): Response
    {
        $order = $orderRepository->findBy([],['date' => 'ASC']);
        $chart = $chartBuilder->createChart(Chart::TYPE_LINE);
        $labels = [];
        $datasets = [];
        foreach ($order as $item)
        {
            $labels[] = $item->getDate()->format('y-m-d');
            $datasets[] = $item->getTotal();
        }
        $chart->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'My First dataset',
                    'backgroundColor' => 'rgb(255, 99, 132)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => $datasets,
                ],
            ],
        ]);
        $chart->setOptions([
            /*'scales' => [
                'y' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => 100,
                ],
            ],*/
        ]);
        return $this->render('admin/index.html.twig',[
            'orders' => $order,
            'categorise' => $categoryRepository->findAll(),
            'users' => $userRepository->findAll(),
            'products' => $productRepository->findAll(),
            'chart' => $chart
        ]);
    }

    #[Route('/profile', name: 'app_admin_profile')]
    public function profile(Request $request,UserRepository $repository,FileUploader $upload,UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $form_profile  = null;
        $user = $this->getUser();
        $form_password = $this->createForm(ChangePassType::class,$user);
        $form_password->handleRequest($request);
        if ($form_password->isSubmitted() && $form_password->isValid()) {
            if ($userPasswordHasher->isPasswordValid($user,$form_password->get('courant')->getData()))
            {
                $user->setPassword($userPasswordHasher->hashPassword($user,$form_password->get('newPassword')->getData()));
                $repository->save($user, true);
                return $this->redirectToRoute('app_logout');
            }
            else{
                $this->addFlash('danger','Courant Password Not Mach ?');
                return $this->redirectToRoute('app_admin_profile');
            }
        }

        if ($user->getProfile())
        {
            $profile = $user->getProfile();
            $photo = $profile->getPhoto();
            $form_profile = $this->createForm(ProfileType::class,$profile);
            $form_profile->handleRequest($request);
            if ($form_profile->isSubmitted() && $form_profile->isValid()) {
                if ($request->files->get('profile')['photo'])
                {
                    $profile->setPhoto($upload->update($form_profile->get('photo')->getData(),$this->getParameter('upload_directory'),'user',$photo));
                }
                $user->setProfile($profile);
                $repository->save($user, true);
                return $this->redirectToRoute('app_admin_profile');
            }
        }
        else
        {
            $profile = new Profile();
            $form_profile = $this->createForm(ProfileType::class, $profile);
            $form_profile->handleRequest($request);
            if ($form_profile->isSubmitted() && $form_profile->isValid()) {
                if ($request->files->get('profile')['photo'])
                {
                    $profile->setPhoto($upload->upload($form_profile->get('photo')->getData(),$this->getParameter('upload_directory'),'user'));
                }
                $user->setProfile($profile);
                $repository->save($user,true);
                return $this->redirectToRoute('app_admin_profile');
            }
        }
//        dd('zzz');
        return $this->renderForm('admin/profile/index.html.twig',['form_profile' => $form_profile,'form_password' => $form_password]);
    }
}
